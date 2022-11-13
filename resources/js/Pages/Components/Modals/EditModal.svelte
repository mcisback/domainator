<script>
    import Modal from "./Modal.svelte";

    export let props = {}

    console.log('EditModal props: ', props)

    const {
        record,
        format
    } = props

    const columns = Object.keys(record)

    let showModal = false
</script>

<div>
    <Modal title={"Modifica"}>
        <form>
            {#each columns as col, i}
                <div class="mb-3 row">
                    <label for={col} class="col-sm-2 col-form-label">{col}</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id={col} value={record[col]}>
                    </div>
                </div>
            {/each}
        </form>
    </Modal>
</div>

<style>
    /*#overlay {*/
    /*    background-color: #ffffff50;*/
    /*    top: 0;*/
    /*    left: 0;*/
    /*    width: 100%;*/
    /*    height: 100%;*/
    /*    bottom: 0;*/
    /*}*/
</style>
