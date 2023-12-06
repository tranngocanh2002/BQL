<div class="modal fade" id="modal-import-success">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= Yii::t('backendQltt', 'Import dữ liệu') ?></h4>
            </div>
            <div class="modal-body">
                <div class="succes-icon text-center">
                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAA3NCSVQICAjb4U/gAAAACXBIWXMAAADgAAAA4AGiX/7KAAAAGXRFWHRTb2Z0d2FyZQB3d3cuaW5rc2NhcGUub3Jnm+48GgAAAW5QTFRF////JLZtQL+AOcZxJ7F2D6VpNb+AM7h6K7N3KLd4HrJ3M7t9KLN5Mbl6Hax0HKxzMrl8M7p7Mrp8L7h7H611GKlzHq11Ha10Mrp8G6x0Mrp8Mrp8Lrd6M7p8GapzE6ZxMrt8EaVwMrp8Mrp8E6ZxE6ZxEaRwEKRwEaRwDaJvDqNvD6RvCqBuEqVxE6ZxFadyF6lzGqt0Jap+K7CAMLl7Mbl8MrCGMrp8M7CGNLCHNLt+NbGINbt+NrGINrt/N7KJOLKJOLyAObKKPL6DPb6DPr6EQb+FQb+GQ8CHRcCITMOMUsWRU8WRVMWSVcaSVcaTVr6bVsaTV8aUWb+caMijbc6ifNOrftOsgNSugdWugs+1j9m4k9q6p+LHuefTz+/g0O/h0vDi1PDj4vbt5PXv5Pbu5vfv6vfy6/fz7Pj07Pnz7fj07fnz7fn07vj17vn07/r18Pr18fr29vz59vz6+Pz7/P79/f79/f7+////jYKxlQAAACx0Uk5TAAcICQ0RGBkeICstOUlyeISXmJ2uvr7BwcTI2t3j5O/y8/P19vj6/f3+/v6hmObYAAABb0lEQVQ4y3WT91vCMBCGAyqgWG3rqkUUwYXGjcaNA/fee0/cW/Lf2zRpSGn5fuiT3vulzV3uAODySKoWikZDmip5gFN+JYK4Ioo/B3vlGLIpJntF7tORQ7ovy4Nh5KJwkO935YaDfcOrozzS6TlkNzZhPmUzv5gTL59/nY6RXEi2ipOvPGKMz0aMlWLUL+Lgq8+YaIdUzAMkB197MTk+6jVeJKCyQ+0uMr7O+Gsf7BlAKtDM6NIt/j2g/I3xaQhhd0IDIRKdvzNCmX1jtflO+dMkJOqsA1FiODaDmT20ZfEkpGqlhkMa/jv5oIv0OOMwTn8xdYFFpUctDpvYIWdEx8Mw57DeSjN1yfm9wGENL1TqyuJDAodl2VLPXpv8Z1Dk8SLhsuY+SaobIoe1tuteuMHf2zbeEbA3TKIf2lWZ23KJLhtvLHA0rc3RUuLS9oKjudR1cLijoTjP6FFHe1Vh3uE1HG3VAZf55uNfUS6M/z+XncW7EALaYwAAAABJRU5ErkJggg==" alt="">
                </div>
                <div class="table-return">
                    <table class="table text-center" id="table-return">
                        <thead>
                            <tr>
                                <th style="width: 120px;"><?= Yii::t('backendQltt', 'Tổng số') ?></th>
                                <th><?= Yii::t('backendQltt', 'Thành công') ?></th>
                                <th><?= Yii::t('backendQltt', 'Lỗi') ?></th>
                            </tr>
                        </thead>
                        <tbody data-content="table-return">
                            
                        </tbody>
                    </table>
                </div>
                <div class="table-error">
                    <table class="dataTables_wrapper form-inline dt-bootstrap table" id="table-error">
                        <thead>
                            <tr>
                                <th><?= Yii::t('backendQltt', 'Dòng') ?></th>
                                <th><?= Yii::t('backendQltt', 'Lỗi') ?></th>
                            </tr>
                        </thead>
                        <tbody data-content="table-error">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        $(function () {
            $('#table-return').DataTable();
        });
    }, false);
</script> -->