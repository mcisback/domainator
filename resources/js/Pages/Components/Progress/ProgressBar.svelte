<script>
    export let max = 100
    export let count = 0

    $: step = (100/max)*count
</script>

<div class="progress">
    <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style={`width: ${step}%;`} aria-valuenow={step} aria-valuemin="0" aria-valuemax={max}>
        {step}%
    </div>
</div>
