<?php

use yii\helpers\Url;

$this->title = Yii::t('backend', Yii::$app->name);
$page = 1;
if (!empty($_GET['page'])) {
    $page = $_GET['page'];
}
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.min.js"></script>
<!--<script src="https://unpkg.com/pdf-lib"></script>-->

<iframe id="pdf" style="width: 100%; height: 100%;"></iframe>
<style>
    .page-break {
        page-break-after: always;
        page-break-inside: avoid;
        clear:both;
    }
    .page-break-before {
        page-break-before: always;
        page-break-inside: avoid;
        clear:both;
    }
</style>
<script>
    var doc = new jsPDF();
    // var doc = new jsPDF('p', 'pt', 'a4');

    // var specialElementHandlers = {
    //     '#editor': function (element, renderer) {
    //         return true;
    //     }
    // };
    //

    function a() {
        alert('a')
        for(var i = 0; i < $('.grid').length - 1; i++){
            doc.fromHTML($('.grid')[i]);
            doc.addPage();
        }
        doc.save('hello.pdf');

        // doc.fromHTML($('#container').html());
        // doc.addPage(100, $('.grid').height()*2);
        // doc.save('sample-file.pdf');

        // doc.addHTML($("#container").html(), function () {
        //     var string = doc.output('datauristring');
            // doc.save("test.pdf");
        // });
    };

    // createPdf();

    // modifyPdf();
    async function createPdf() {
        const pdfDoc = await PDFLib.PDFDocument.create();
        const page = pdfDoc.addPage([350, 400]);
        // const page = pdfDoc.html();
        page.moveTo(110, 200);
        // page.drawText('Hello World!');
        page.html('<div>Hello World!</div>');
        const pdfDataUri = await pdfDoc.saveAsBase64({dataUri: true});
        document.getElementById('pdf').src = pdfDataUri;
    }

    async function modifyPdf() {
        const url = 'https://pdf-lib.js.org/assets/with_update_sections.pdf'
        const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
        const pdfDoc = await PDFLib.PDFDocument.load(existingPdfBytes)
        // const helveticaFont = await pdfDoc.embedFont(StandardFonts.Helvetica)

        const pages = pdfDoc.getPages()
        const firstPage = pages[0]
        const {width, height} = firstPage.getSize()
        firstPage.drawText('This text was added with JavaScript!')

        const pdfBytes = await pdfDoc.save()
        // const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
        // document.getElementById('pdf').src = pdfDataUri;
    }
</script>
<div class="site-index">
    <button id="cmd" onclick="a()">generate PDF</button>
    <div id="wrap">
        <div class="container" id="container">
            <?php for ($i = 0; $i < $page; $i++){ ?>
                <div class="grid">
                    <div class="row">
                        <div class="span12">
                            <h2 id="installation">Installation</h2>
                            <pre><code class="lang-bash">npm <span class="token function">install</span> jsreport-phantom-pdf</code></pre>
                            <h2 id="basic-settings">Basic settings</h2>
                            <ul>
                                <li><code>margin</code> - px or cm specification of margin used from page borders, you can
                                    also pass an <code>Object</code> or <code>JSON object string</code> for better control
                                    of each margin side. ex: <code>{ "top": "5px", "left": "10px", "right": "10px",
                                        "bottom": "5px" }</code></li>
                                <li><code>format</code> - predefined page sizes containing A3, A4, A5, Legal, Letter</li>
                                <li><code>width</code> - px or cm page width, takes precedence over paper format</li>
                                <li><code>height</code> - px or cm page height, takes precedence over paper format</li>
                                <li><code>orientation</code> - portrait or landscape orientation</li>
                                <li><code>headerHeight</code> - px or cm height of the header in the page</li>
                                <li><code>header</code> - header html content</li>
                                <li><code>footerHeight</code> - px or cm height of the footer in the page</li>
                                <li><code>footer</code> - footer html content</li>
                                <li><code>printDelay</code> - delay between rendering a page and printing into pdf, this is
                                    useful when printing animated content like charts
                                </li>
                                <li><code>blockJavaScript</code> - block executing javascript</li>
                                <li><code>waitForJS</code> - true/false</li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div id="editor"></div>
</div>
