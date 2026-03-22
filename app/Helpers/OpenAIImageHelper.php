<?php

namespace App\Helpers;

class OpenAIImageHelper
{
    private $apiKey;
    private $apiUrl = 'https://api.openai.com/v1/images/generations';

    public function __construct()
    {
        // Get API key from environment or fallback to AI Writer settings
        $key = getenv('OPENAI_API_KEY') ?: '';
        if (empty($key)) {
            try {
                $ai = \aiWriter();
                if (!empty($ai) && !empty($ai->apiKey)) {
                    $key = $ai->apiKey;
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }
        $this->apiKey = $key ?: '';
    }

    /**
     * Generate image using OpenAI GPT-Image or DALL-E
     *
     * @param string $prompt The text prompt for image generation
     * @param string $model The model to use (default: gpt-image-1)
     * @param string $size The size of the generated image
     * @param string $quality The quality of the image
     * @param int $n Number of images to generate (1-10 for dall-e-2, only 1 for newer models)
     * @return array|false
     */
    public function generateImage($prompt, $model = 'gpt-image-1', $size = '1024x1024', $quality = 'standard', $n = 1)
    {
        if (empty($this->apiKey)) {
            log_message('error', 'OpenAI API key not configured');
            return false;
        }

        if (empty($prompt)) {
            log_message('error', 'Image generation prompt is empty');
            return false;
        }

        // Validate model
        $allowedModels = ['dall-e-2', 'dall-e-3', 'gpt-image-1'];
        if (!in_array($model, $allowedModels)) {
            $model = 'gpt-image-1';
        }

        // Validate size based on model
        if ($model === 'gpt-image-1') {
            $allowedSizes = ['1024x1024', '1536x1024', '1024x1536'];
            $n = 1; // GPT-Image-1 only supports n=1
        } elseif ($model === 'dall-e-3') {
            $allowedSizes = ['1024x1024', '1792x1024', '1024x1792'];
            $n = 1; // DALL-E 3 only supports n=1
        } else {
            $allowedSizes = ['256x256', '512x512', '1024x1024'];
        }

        if (!in_array($size, $allowedSizes)) {
            $size = '1024x1024';
        }

        // Validate quality based on model
        if ($model === 'gpt-image-1') {
            $allowedQualities = ['low', 'medium', 'high'];
            if (!in_array($quality, $allowedQualities)) {
                $quality = 'high'; // Default for gpt-image-1
            }
        } else {
            // For DALL-E models
            $allowedQualities = ['standard', 'hd'];
            if (!in_array($quality, $allowedQualities)) {
                $quality = 'standard';
            }
        }

        // Prepare request data
        $requestData = [
            'model' => $model,
            'prompt' => $prompt,
            'size' => $size,
            'n' => min(max(1, $n), $model === 'dall-e-3' ? 1 : 10)
        ];

        // Add quality parameter based on model
        if ($model === 'gpt-image-1') {
            $requestData['quality'] = $quality;
            // Add output format for gpt-image-1
            $requestData['output_format'] = 'png';
        } elseif ($model === 'dall-e-3') {
            $requestData['quality'] = $quality;
        }

        try {
            $response = $this->makeApiRequest($requestData);
            
            if ($response && isset($response['data']) && !empty($response['data'])) {
                return [
                    'success' => true,
                    'data' => $response['data'],
                    'created' => $response['created'] ?? time()
                ];
            } else {
                log_message('error', 'Invalid response from OpenAI API: ' . json_encode($response));
                if ($model === 'gpt-image-1') {
                    return $this->generateImageWithFallback($prompt, $size);
                }
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'OpenAI API error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate image optimized for web stories
     *
     * @param string $prompt The text prompt for image generation
     * @param string $style Optional style modifier
     * @return array|false
     */
    public function generateWebStoryImage($prompt, $style = '')
    {
        // Optimize prompt for web stories
        $optimizedPrompt = $this->optimizePromptForWebStories($prompt, $style);

        // Read defaults from environment (with sensible fallbacks)
        $model = getenv('OPENAI_DEFAULT_MODEL') ?: 'gpt-image-1';
        $size = getenv('OPENAI_DEFAULT_SIZE') ?: ($model === 'dall-e-3' ? '1024x1792' : '1024x1536');
        $quality = getenv('OPENAI_DEFAULT_QUALITY') ?: ($model === 'gpt-image-1' ? 'high' : 'hd');

        // Validate model and adapt size/quality accordingly
        $allowedModels = ['dall-e-2', 'dall-e-3', 'gpt-image-1'];
        if (!in_array($model, $allowedModels)) {
            $model = 'gpt-image-1';
        }

        // Ensure size belongs to selected model
        $allowedSizesByModel = [
            'gpt-image-1' => ['1024x1024', '1536x1024', '1024x1536'],
            'dall-e-3' => ['1024x1024', '1792x1024', '1024x1792'],
            'dall-e-2' => ['256x256', '512x512', '1024x1024'],
        ];
        $allowedSizes = $allowedSizesByModel[$model];
        if (!in_array($size, $allowedSizes)) {
            // choose portrait as default where applicable
            $size = $allowedSizes[count($allowedSizes) - 1];
        }

        // Validate quality
        if ($model === 'gpt-image-1') {
            $allowedQualities = ['low', 'medium', 'high'];
            if (!in_array($quality, $allowedQualities)) {
                $quality = 'high';
            }
        } elseif ($model === 'dall-e-3') {
            $allowedQualities = ['standard', 'hd'];
            if (!in_array($quality, $allowedQualities)) {
                $quality = 'hd';
            }
        } else {
            // dall-e-2 doesn't support quality parameter; ignore
            $quality = 'standard';
        }

        return $this->generateImage(
            $optimizedPrompt,
            $model,
            $size,
            $quality
        );
    }

    /**
     * Make HTTP request to OpenAI API
     *
     * @param array $data Request data
     * @return array|false
     */
    private function makeApiRequest($data)
    {
        $ch = curl_init();

        $timeout = intval(getenv('OPENAI_IMAGE_TIMEOUT') ?: 60);
        if ($timeout < 20) { $timeout = 20; }
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            log_message('error', 'CURL error: ' . $error);
            return false;
        }

        if ($httpCode !== 200) {
            log_message('error', 'OpenAI API returned HTTP ' . $httpCode . ': ' . $response);
            log_message('error', 'Request data was: ' . json_encode($data));
            
            // Check if it's a gpt-image-1 verification error and try fallback to dall-e-3
            if ($httpCode === 403 && strpos($response, 'gpt-image-1') !== false && strpos($response, 'verified') !== false) {
                log_message('info', 'gpt-image-1 requires verification, falling back to dall-e-3');
                return $this->generateImageWithFallback($data['prompt'], $data['size']);
            }
            
            return false;
        }

        $decodedResponse = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'JSON decode error: ' . json_last_error_msg());
            return false;
        }

        return $decodedResponse;
    }

    /**
     * Optimize prompt for web stories format
     *
     * @param string $prompt Original prompt
     * @param string $style Style modifier
     * @return string Optimized prompt
     */
    private function optimizePromptForWebStories($prompt, $style = '')
    {
        $optimizedPrompt = $prompt;

        // Merge env brand style with provided style
        $envStyle = getenv('OPENAI_BRAND_STYLE') ?: '';
        $styleCombined = trim(($envStyle ? ($envStyle . ' ') : '') . ($style ?: ''));

        // Include style if any
        if (!empty($styleCombined)) {
            $optimizedPrompt = $styleCombined . ' style, ' . $optimizedPrompt;
        }

        // Add theme brand colors if available
        $brandColors = '';
        try {
            $theme = \Config\Globals::$activeTheme ?? null;
            if (!empty($theme)) {
                $c1 = $theme->theme_color ?? '';
                $c2 = $theme->block_color ?? '';
                $c3 = ($theme->theme != 'classic') ? ($theme->mega_menu_color ?? '') : '';
                $palette = array_filter([$c1, $c2, $c3]);
                if (!empty($palette)) {
                    $brandColors = implode(' ', $palette);
                }
            }
        } catch (\Throwable $e) {}

        if (!empty($brandColors)) {
            $optimizedPrompt .= ', harmonize with brand color palette (' . $brandColors . ')';
        }

        // Add web story specific optimizations (photorealism + central safe area for text)
        $optimizedPrompt .= ', photorealistic, realistic textures, natural lighting, shallow depth of field, '
            . 'engaging yet clean composition, mobile-friendly vertical format, professional look, '
            . 'no text, no watermark, leave clear negative space in the center for text overlay, '
            . 'safe margins around central area, avoid cluttered background';

        // Ensure prompt is not too long (OpenAI has a limit)
        if (strlen($optimizedPrompt) > 1000) {
            $optimizedPrompt = substr($optimizedPrompt, 0, 1000);
        }

        return $optimizedPrompt;
    }

    /**
     * Validate API key
     *
     * @return bool
     */
    public function validateApiKey()
    {
        if (empty($this->apiKey)) {
            return false;
        }

        // Make a simple request to validate the key
        try {
            $response = $this->generateImage('test', 'gpt-image-1', '1024x1024');
            return $response !== false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get available models
     *
     * @return array
     */
    public function getAvailableModels()
    {
        return [
            'gpt-image-1' => [
                'name' => 'GPT-Image-1 (Latest)',
                'sizes' => ['1024x1024', '1536x1024', '1024x1536'],
                'max_images' => 1,
                'qualities' => ['low', 'medium', 'high'],
                'output_formats' => ['png', 'jpeg', 'webp'],
                'max_resolution' => '4096x4096'
            ],
            'dall-e-3' => [
                'name' => 'DALL-E 3',
                'sizes' => ['1024x1024', '1792x1024', '1024x1792'],
                'max_images' => 1,
                'qualities' => ['standard', 'hd']
            ],
            'dall-e-2' => [
                'name' => 'DALL-E 2',
                'sizes' => ['256x256', '512x512', '1024x1024'],
                'max_images' => 10
            ]
        ];
    }

    /**
     * Generate image with fallback to DALL-E 3
     *
     * @param string $prompt
     * @param string $size
     * @return array|false
     */
    private function generateImageWithFallback($prompt, $size)
    {
        // Convert gpt-image-1 size to dall-e-3 compatible size
        $fallbackSize = $this->convertSizeForFallback($size);
        
        log_message('info', 'Attempting fallback to dall-e-3 with size: ' . $fallbackSize);
        
        return $this->generateImage($prompt, 'dall-e-3', $fallbackSize, 'hd');
    }
    
    /**
     * Convert gpt-image-1 sizes to dall-e-3 compatible sizes
     *
     * @param string $size
     * @return string
     */
    private function convertSizeForFallback($size)
    {
        $sizeMapping = [
            '1024x1536' => '1024x1792', // Portrait
            '1536x1024' => '1792x1024', // Landscape
            '1024x1024' => '1024x1024'  // Square
        ];
        
        return $sizeMapping[$size] ?? '1024x1792';
    }
    
    /**
     * Set API key
     *
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Get current API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
