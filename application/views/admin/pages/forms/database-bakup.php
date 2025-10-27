<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Database Backup</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a class="text text-info"
                                href="<?= base_url('admin/home') ?>"><?= display_breadcrumbs(); ?></a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Select Tables for Backup</h3>
                </div>
                <div class="card-body">
                    <!-- <form id="backupForm"> -->
                    <form id="backupForm" method="POST" action="<?= base_url('admin/database_bakup/backup') ?>">
                        <div class="form-group">
                            <div>
                                <input type="checkbox" id="select_all" name="select_all"> <label for="select_all">Full Database Bakup</label>
                            </div>
                            <div id="tableList">
                                <div class="row">
                                    <?php foreach ($tables as $table): ?>
                                        <div class="col-md-2">
                                            <input type="checkbox" class="table_checkbox" name="tables[]" value="<?= $table; ?>" id="table_<?= $table; ?>">
                                            <label for="table_<?= $table; ?>"><?= $table; ?></label>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn-info">Take Backup</button>
                    </form>
                    <!-- Invisible iframe to handle file download -->
                    <iframe id="downloadFrame" name="downloadFrame" style="display: none;"></iframe>
                </div>
            </div>
        </div>
    </section>
</div>