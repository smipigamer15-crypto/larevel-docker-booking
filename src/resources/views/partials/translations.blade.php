@php
    $translationFile = lang_path(app()->getLocale().'.json');
    $siteTranslations = is_file($translationFile) ? json_decode(file_get_contents($translationFile), true) : [];
@endphp
<script type="application/json" id="site-translations">@json($siteTranslations, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)</script>
