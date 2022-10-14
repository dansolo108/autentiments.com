{extends 'template:1'}
{block 'main'}
    <main class="page-container au-certificate">
        {'AjaxForm' | snippet : [
            'snippet'=>'certificate',
            'form'=>'certificate',
            'validationErrorMessage'=>'Обязательные поля не заполнены',
        ]}
    </main>
{/block}