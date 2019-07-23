
{* file to extend *}
{extends file="parent:frontend/index/index.tpl"}

{* main content *}
{block name='frontend_index_content'}
<script>
    fully.scanQrCode("{$ostQrCodeReaderPrompt}", "{url controller='OstQrCodeReader' action='open'}/?q=$code", -1, -1, true, true)
</script>
{/block}
