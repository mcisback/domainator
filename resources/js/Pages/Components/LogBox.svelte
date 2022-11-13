<script>

    import Spinner from "./Spinners/Spinner.svelte";

    export let form
    export let title
    export let action = ''
    export let method = 'POST'
    export let csrf_token
    export let onSubmit = null
    export let showSpinner = false

    const condionalSubmit = (node) => {
        if(!!onSubmit) {
            node.addEventListener('submit', (e) => {
                e.preventDefault();

                return onSubmit(e, form)
            })
        }

        return {
            destroy() {
                node.removeEventListener('submit', onSubmit)
            }
        }
    }
</script>

<div id="logbox">
    <div class="row p-4">
        <div class="col-md-8 mx-auto d-flex justify-content-center align-items-center">
            <div style="width: 12%;">
                <img src="/img/logo1.png" alt="Logo" class="w-100">
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{title}</div>

                <div class="card-body">

                    <slot name="beforeForm"></slot>

                    <form bind:this={form} method={method} action={action ? action : ''}
                          use:condionalSubmit>
                        {#if csrf_token}
                            <input type="hidden" name="_token" value={csrf_token}>
                        {/if}

                        <slot></slot>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {title}
                                </button>

                                <slot name="submit"></slot>
                            </div>
                        </div>

                        {#if showSpinner}
                            <div class="row mb-0 mt-4">
                                <div class="col mx-auto text-center">
                                    <Spinner />
                                </div>
                            </div>
                        {/if}

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #logbox {
        padding-top: 25%;
    }

    button[type="submit"]:not(:disabled) {
        background-color: #b125e2;
        border-color: #b125e2;
    }

    /*a:hover {*/
    /*    color: #b125e2;*/
    /*}*/

    .card-header {
        background-color: #b125e2;
        color: white;
        font-size: 1.5rem;
        text-align: center;
    }

    .card-body {
        padding: 4rem;
    }
</style>
