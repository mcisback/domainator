<script>
    import { createEventDispatcher } from 'svelte';

    import EditModal from "./EditModal.svelte";

    export let id='modal'
    export let props
    export let title = 'Modal'

    let toggle = true

    const dispatch = createEventDispatcher();

    function clickOutside(node) {

        const handleClick = event => {
            if (node && !node.contains(event.target) && !event.defaultPrevented) {
                node.dispatchEvent(
                    // new CustomEvent('modal.clickOutside', node)

                    dispatch('modal.clickOutside', {
                        id,
                    })

                    // close()
                )
            }
        }

        document.addEventListener('click', handleClick, true);

        return {
            destroy() {
                document.removeEventListener('click', handleClick, true);
            }
        }
    }

    const close = () => {
        toggle = false

        // document.dispatchEvent(new CustomEvent("closemodal"))

        dispatch('modal.closed', {
            id,
        });
    }

</script>

{#if toggle}
    <div use:clickOutside class="modal fade show lp-modal" id={id} tabindex="-1" aria-labelledby={`${id}Label`} aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto text-center" id={`${id}Label`}>{title}</h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close" on:click={close}></button>
                </div>
                <div class="modal-body">
                    <slot {...props}></slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-backdrop fade show lp-backgrop" id={`${id}_backdrop`}></div>
{/if}

<style>
    .lp-modal, .lp-backgrop {
        display: block !important;
    }
</style>
