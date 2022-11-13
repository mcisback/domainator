<script>
    export let id = null
    export let checked = false
</script>

<div class="form-check form-check-inline form-switch red">
    <input class="form-check-input" type="checkbox" id={id} bind:checked={checked}>
    <label class="form-check-label" style="white-space: nowrap;" for={id}>
        <slot></slot>
    </label>
</div>

