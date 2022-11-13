<script>
    import moment from "moment";

    export let name = 'date'
    export let label = 'Date:'

    let date = new Date()
    let hours = date.getHours()
    let minutes = date.getMinutes()
    let dateString = date.toISOString().substring(0, 10)

    const setDate = () => {
        const _date = new Date(dateString)
        _date.setHours(hours)
        _date.setMinutes(minutes)

        date = _date.toISOString()

        console.log('date: ', date)
    }
</script>

<div class="row w-100">
    <input bind:value={date} type="hidden" name={name} required>
    <label for={name}>{label}</label>
    <div class="col">
        <input bind:value={dateString} on:change={setDate} type="date" name={`${name}_date`} id={name} class="form-control" exclude>
    </div>
    <div class="col-2 d-flex align-items-center">
        <label for={`${name}_hours`} >Ora:</label>
        <input bind:value={hours} on:input={setDate} type="number" min="0" max="24" name={`${name}_hours`} id={`${name}_hours`} class="form-control" exclude>
    </div>
    <div class="col-2 d-flex align-items-center">
        <label for={`${name}_minutes`} >Min:</label>
        <input bind:value={minutes} on:input={setDate} type="number" min="0" max="60" name={`${name}_minutes`} id={`${name}_minutes`} class="form-control" exclude>
    </div>
</div>

<style>
    label {
        margin-right: 0.5rem;
    }
</style>
