<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Detalhes do Lead</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <strong>Nome:</strong>
                        <p><?= esc($lead->name); ?></p>
                    </div>
                    <div class="col-sm-4">
                        <strong>Email:</strong>
                        <p><?= esc($lead->email); ?></p>
                    </div>
                    <div class="col-sm-4">
                        <strong>Telefone:</strong>
                        <p><?= esc($lead->phone); ?></p>
                    </div>
                </div>
                <hr>
                <?php if (!empty($lead->sim_data)): 
                    $simData = json_decode($lead->sim_data);
                    if ($simData && isset($simData->companyInputs)): ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>Dados da Empresa e Necessidade</h4>
                            </div>
                            <div class="col-sm-4">
                                <strong>Valor da Necessidade:</strong>
                                <p><?= esc($simData->companyInputs->valorNecessidade); ?></p>
                            </div>
                            <div class="col-sm-4">
                                <strong>Prazo (meses):</strong>
                                <p><?= esc($simData->companyInputs->numParcelas); ?></p>
                            </div>
                            <div class="col-sm-4">
                                <strong>Objetivo do Crédito:</strong>
                                <p><?= esc($simData->companyInputs->objetivoCredito); ?></p>
                            </div>
                            <div class="col-sm-4">
                                <strong>Faturamento Anual:</strong>
                                <p><?= esc($simData->companyInputs->faturamentoAnual); ?></p>
                            </div>
                            <div class="col-sm-4">
                                <strong>EBITDA Anual:</strong>
                                <p><?= esc($simData->companyInputs->ebitdaAnual); ?></p>
                            </div>
                            <div class="col-sm-4">
                                <strong>Dívida Bruta Atual:</strong>
                                <p><?= esc($simData->companyInputs->dividaBrutaAtual); ?></p>
                            </div>
                        </div>
                        <hr>
                    <?php endif; 
                    if ($simData && isset($simData->simulatedLines) && is_array($simData->simulatedLines)): ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>Linhas de Crédito Simuladas</h4>
                            </div>
                            <?php foreach ($simData->simulatedLines as $line): ?>
                                <div class="col-sm-12">
                                    <div class="box box-widget">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><?= esc($line->nome); ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <strong>Taxa Nominal Anual:</strong>
                                                    <p><?= esc($line->taxaNominalAnual); ?></p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <strong>Parcela Estimada:</strong>
                                                    <p><?= esc($line->parcela); ?></p>
                                                </div>
                                                <?php if (!empty($line->iof)): ?>
                                                <div class="col-sm-4">
                                                    <strong>IOF Estimado:</strong>
                                                    <p><?= esc($line->iof); ?></p>
                                                </div>
                                                <?php endif; ?>
                                                <?php if (!empty($line->tac)): ?>
                                                <div class="col-sm-4">
                                                    <strong>TAC Estimada:</strong>
                                                    <p><?= esc($line->tac); ?></p>
                                                </div>
                                                <?php endif; ?>
                                                <?php if (!empty($line->custoEstruturacao)): ?>
                                                <div class="col-sm-4">
                                                    <strong>Custo Estruturação:</strong>
                                                    <p><?= esc($line->custoEstruturacao); ?></p>
                                                </div>
                                                <?php endif; ?>
                                                <?php if (!empty($line->custoAnalise)): ?>
                                                <div class="col-sm-4">
                                                    <strong>Custo Análise:</strong>
                                                    <p><?= esc($line->custoAnalise); ?></p>
                                                </div>
                                                <?php endif; ?>
                                                <div class="col-sm-4">
                                                    <strong>Valor Líquido Liberado:</strong>
                                                    <p><?= esc($line->valorLiberado); ?></p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <strong>CET Mensal Estimado:</strong>
                                                    <p><?= esc($line->cetMensal); ?></p>
                                                </div>
                                                <div class="col-sm-4">
                                                    <strong>CET Anual Estimado:</strong>
                                                    <p><?= esc($line->cetAnual); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">Nenhum dado de simulação disponível para este lead.</div>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <a href="<?= adminUrl('simulator-leads'); ?>" class="btn btn-default">Voltar para a Lista</a>
            </div>
        </div>
    </div>
</div>