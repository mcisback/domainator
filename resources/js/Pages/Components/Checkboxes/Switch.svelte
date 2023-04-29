<script>
    export let id = null
    export let checked = false
    export let style = ''
    export let onChange = () => {}
</script>

<div class="form-check form-check-inline form-switch red" style={style}>
    <input class="form-check-input" type="checkbox" id={id} bind:checked={checked} on:change={onChange}>
    <label class="form-check-label" style="white-space: nowrap;" for={id}>
        <slot></slot>
    </label>
</div>

