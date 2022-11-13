<script>
    export let title = 'Live Lesson'
    export let roomUrl = 'https://marco-caggiano-labs.whereby.com/test0b920df8-6f31-4ea3-accf-da84c4c1cb98'
</script>

<div class="w-100">
    <iframe class="w-100" title={title} src={roomUrl} allow="camera; microphone; fullscreen; speaker; display-capture"></iframe>
</div>

<style>
    iframe {
        height: 80vh;
    }
</style>
