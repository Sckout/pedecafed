<?php echo $this->extend('Admin/layout/principal'); ?>


<--<!-- Aqui colocamos complemento de titulo na view atual -->
<?php echo $this->section('titulo') ?> <?php echo $titulo; ?> <?php echo $this->endSection() ?>




<?php echo $this->section('estilos') ?>

<--<!-- Aqui Enviamos para o template principal os estilos -->

<link rel="stylesheet" href="<?php echo site_url('admin/vendors/auto-complete/jquery-ui.css'); ?>"/>


<?php echo $this->endSection() ?>


<?php echo $this->section('conteudo') ?>

<div class="row">

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"><?php echo $titulo; ?></h4>


                <div class="ui-widget text-center">
                    <input id="query" name="query" placeholder="Pesquise por um entregador" class="form-control bg-light mb-5">
                </div>

                <a href="<?php echo site_url("admin/entregadores/criar") ?>" class="btn btn btn-success float-right mb-5">
                    <i class="mdi mdi-plus btn-icon-prepend "></i>
                    Cadastrar  
                </a>

                <?php if (empty($entregadores)): ?>

                    <p>Não há dados para exibir!</p>

                <?php else : ?>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Imagem</th>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Placa</th>
                                    <th>Ativo</th>
                                    <th>Situação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($entregadores as $entregador): ?>
                                    <tr>
                                        <td class="py-1">

                                            <?php if ($entregador->imagem): ?>
                                                <img src="<?php echo site_url("admin/entregadores/imagem/$entregador->imagem"); ?>" alt="<?php echo esc($entregador->nome); ?>"/>
                                            <?php else: ?>
                                                <img src="<?php echo site_url('admin/images/default-user.png'); ?>" alt="Entregador sem foto"/>
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="<?php echo site_url("admin/entregadores/show/$entregador->id"); ?>"><?php echo $entregador->nome; ?></a></td>
                                        <td><?php echo $entregador->telefone; ?></td>
                                        <td><?php echo $entregador->placa; ?></td>
                                        <td><?php echo ($entregador->ativo && $entregador->deletado_em == null ? '<label class="badge badge-pill badge-primary">Sim</label>' : '<label class="badge badge-pill badge-danger">Não</label>') ?></td>
                                        <td><?php echo ($entregador->deletado_em == null ? '<label class="badge badge-pill badge-primary">Disponivel</label>' : '<label class="badge badge-pill badge-danger">Excluido</label>') ?>
                                            <?php if ($entregador->deletado_em != null): ?>
                                                <a href="<?php echo site_url("admin/entregadores/desfazerexclusao/$entregador->id") ?>" class="badge badge-pill badge-dark ml-2">
                                                    <i class="mdi mdi-undo btn-icon-prepend "></i>
                                                    Desfazer 
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>  

                            </tbody>
                        </table>

                        <div class="mt-3">

                            <?php echo $pager->links() ?>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>

</div>


<?php echo $this->endSection() ?>




<?php echo $this->section('scripts') ?>

<--<!-- Aqui Enviamos para o template principal os scripts -->
<script 
    src="<?php echo site_url('admin/vendors/auto-complete/jquery-ui.js'); ?>">
</script>

<script>

    $(function () {
        $("#query").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "<?php echo site_url('admin/entregadores/procurar'); ?>",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        if (data.length < 1) { //nao foi encontrado nada
                            var data = [
                                {
                                    label: 'Entregador não encontrado',
                                    value: -1
                                }
                            ];
                        }
                        response(data); //aqui temos valor no data
                    },
                }); //fim ajax
            },
            minLenght: 1,
            select: function (event, ui) {
                if (ui.item.value == -1) {
                    $(this).val(""); //retorna valor nao selecionavel
                    return false;
                } else {
                    window.location.href = '<?php echo site_url('admin/entregadores/show/'); ?>' + ui.item.id; //retorna e concatena com o valor de ui
                }
            }
        }); //fim auto complete
    });

</script>

<?php echo $this->endSection(); ?>