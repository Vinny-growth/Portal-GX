<div class="row">
    <div class="col-sm-12">
        <section class="content-header"><h1>Editar Página</h1></section>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-body">
                <form method="post" action="<?= adminUrl('cms-pages/edit/'.(int)$page->id); ?>">
                    <?= csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Título</label>
                            <input type="text" class="form-control" name="title" value="<?= esc($page->title); ?>">
                        </div>
                        <div class="col-md-6">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" value="<?= esc($page->slug); ?>">
                        </div>
                    </div>
                    <div class="mt-15">
                        <a class="btn btn-default" href="<?= adminUrl('cms-pages'); ?>">Voltar</a>
                        <button class="btn btn-primary">Salvar</button>
                        <a class="btn btn-info" href="<?= adminUrl('cms-pages/builder/'.(int)$page->id); ?>">Abrir Builder</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
