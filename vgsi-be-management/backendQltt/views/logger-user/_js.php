<script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function () {
            let action = $('form#w0').attr('action'); 
            let isCreate = action ? action.search('create') : -1;
            
            $('.nav-tabs > li a[title]').tooltip();
            //Wizard
            $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

                var $target = $(e.target);
            
                if ($target.parent().hasClass('disabled')) {
                    return false;
                }
            });

            $(".skip-btn").click(function (e) {
                let actionNew = isCreate < 0 ? `${action}&isTrash=false` : `${action}?isTrash=false`;
                $('form#w0').attr('action', actionNew);
                $('form#w0').submit();
            });

            $(".trash").click(function (e) {
                let actionNew = isCreate < 0 ? `${action}&isTrash=true` : `${action}?isTrash=true`;
                $('form#w0').attr('action', actionNew);
                $('form#w0').submit();
            });
            
        });

        function nextTab(elem) {
            $(elem).next().find('a[data-toggle="tab"]').click();
        }
        
        function prevTab(elem) {
            $(elem).prev().find('a[data-toggle="tab"]').click();
        }

        function skipTab(elem) {
            $(elem).next().next().find('a[data-toggle="tab"]').click();
        }

        $('#status').change(function() {
            let action = $('form#w0').attr('action'); 
            let isCreate = action ? action.search('create') : -1;
            // if (isCreate < 0) {
                var element = document.getElementById("send_event_at");
                if ($(this).val() == 0 && isCreate < 0) {
                    var $active = $('.wizard .nav-tabs li.active');
                    
                    if ($active.hasClass('step1')) {
                        $active.next().removeClass('disabled');
                        nextTab($active);
                    } else if ($active.hasClass('step3')) {
                        prevTab($active);
                    } 
                    element.classList.add('d-none')
                } else if ($(this).val() == 2) {
                    element.classList.remove("d-none");
                } else {
                    element.classList.add('d-none')
                    element.value = null
                }

                if ($(this).val() !=0) {
                    var $active = $('.wizard .nav-tabs li.active');
                    if ($active.hasClass('step2')) {
                        $active.next().removeClass('disabled');
                        nextTab($active);
                        // $("#trash").prop("disabled", true);
                        // $("#next-step").prop("disabled", false);
                    } else {
                        $active.next().next().removeClass('disabled');
                        skipTab($active);
                    }
                } else {
                    // $("#next-step").prop("disabled", true);
                    // $("#trash").prop("disabled", false);
                }
            // }
        });

        $('#news_template').change(function () {
            var element = document.getElementById("news_template");
            if (element.value && NEWS_TEMPLATE.length > 0) {
                let template = NEWS_TEMPLATE.filter(e => e.id == element.value)

                if (template.length > 0) {
                    template = template[0]
                    $('#announcementcampaign-title').val(template.name)
                    $('#announcementcampaign-title_en').val(template.name_en)
                    $('#announcementcampaign-content').val(template.content_email)
                    $('#ArticleImage').val(template.image)
                    $('#image-preview').attr('src', template.image);

                    CKEDITOR.instances['announcementcampaign-content'].setData(template.content_email);
                }
            }
        });

        // $("#trash").prop("disabled", true);
    }, false);

    const uploadFiles = async () => {
        filesJson = typeof filesJson !== 'object' ? JSON.parse(filesJson) : filesJson
        const files = document.getElementById('files').files;
        const maxFileSizeInBytes = 25 * 1024 * 1024;

        if (files.length > 5 || (filesJson.fileList.length + files.length) > 5) {
            alert("<?= Yii::t('backendQltt', 'Upload tối đa 5 file') ?>");
            return;
        }
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > maxFileSizeInBytes) {
                alert(`File ${files[i].name} size exceeds the maximum limit 25Mb`);
                return;
            } else {
                if (filesJson.fileList.length > 5) {
                    alert("<?= Yii::t('backendQltt', 'Upload tối đa 5 file') ?>");
                    return;
                }
                await upload(files[i])
            }
        }
        
    };

    const upload = (file) => {
        const formData = new FormData();
        formData.append('UploadForm[files][]', file);
        formData.append('fileId', file.name);

        fetch('/upload/tmp', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Csrf-Token': "<?=Yii::$app->request->getCsrfToken()?>",
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(filesJson)
            filesJson.fileList.push(data)
            $("#ArticleAttach").val(JSON.stringify(filesJson));
            generateHtmlFiles()
        })
        .catch(error => {
            console.error('Request failed:', error);
        });
    };

    const movefile = (key, full_path) => {
        delete filesJson.fileList[key]
        document.getElementById("file_" + key).remove();
        var filtered = filesJson.fileList.filter(function (el) {
            return el != null;
        });

        filesJson.fileList = filtered
        document.getElementById("ArticleAttach").value = JSON.stringify(filesJson);
    };

    const generateHtmlFiles = async () => {
        if (filesJson.fileList.length > 0) {
            let html = ''
            for (let i = 0; i < filesJson.fileList.length; i++) {
               html += await htmlDefault(i, filesJson.fileList[i])
               document.getElementById('list-files').innerHTML = html
            }
        }
    };

    const htmlDefault = (key, file) => {
        let fileData = file.full_path.split('/');

        return `<div class="file-item" id="file_${key}">
            <div class="file-name">
                <span>${fileData.slice(-1)[0]}</span>
            </div>
            <div class="delete-icon">
                <button class="delete" type="button" onclick="movefile(${key}, '${fileData.slice(-1)[0]}')">
                    <i class="fa fa-fw fa-close"></i>
                </button>
            </div>
        </div>`;
    };
</script>