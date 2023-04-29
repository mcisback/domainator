<script>
    import { slide } from 'svelte/transition';
    import AlertBox from "../Alerts/AlertBox.svelte";
    import TogglableArrow from "../Widgets/ToggableArrow.svelte";
    import Switch from "../Checkboxes/Switch.svelte";
    import Spinner from "../Spinners/Spinner.svelte";
    import {registerDomain} from "../../PageFunctions/DomainRequests/registerDomain";
    import {updateCurrentDomain, verifyDomainOnSEDO} from "../../PageFunctions/DomainRequests/verifyDomainOnSEDO";
    import {addDomainToSEDO} from "../../PageFunctions/DomainRequests/addDomainToSEDO";
    import {deleteDomain} from "../../PageFunctions/DomainRequests/deleteDomain";
    import {deleteDomainsRequest} from "../../PageFunctions/DomainRequests/deleteDomainsRequest";
    import {promiseChainSequence} from "../../Helpers/promiseChainSequence";
    import SedoSelectAccount from "./SedoSelectAccount.svelte";
    import RegisterDomainsPanel from "./RegisterDomainsPanel.svelte";
    import Tabs from "../Tabs/Tabs.svelte";
    import AddToSEDOPanel from "./AddToSEDOPanel.svelte";
    import TabItem from "../Tabs/TabItem.svelte";
    import VerifyOnSEDOPanel from "./VerifyOnSEDOPanel.svelte";

    export let domainRequests = []
    export let form = {}
    export let currentDomainRequest = null
    export let currentSedoAccountId = null
    export let sedoAccounts = []
    export let sedoCategories = []
    export let sedoLanguages = []
    export let spinners = []



</script>

{#if currentDomainRequest !== null}

    <div class="row mb-3 p-4" transition:slide>

        <div class="row mb-3 fw-bold">
            <Tabs
                items={[
                    {
                        label: 'Register',
                        target: 'register',
                        value: 1,
                        props: {
                            'bind:domainRequests': domainRequests,
                            'bind:currentDomainRequest': currentDomainRequest,
                            'bind:spinners': spinners,
                            'bind:form': form,
                        }
                    },
                    {
                        label: 'Add to SEDO',
                        target: 'addToSEDO',
                        value: 2,
                        props: {
                            'bind:domainRequests': domainRequests,
                            'bind:currentDomainRequest': currentDomainRequest,
                            'bind:spinners': spinners,
                            'bind:form': form,
                        }
                    },
                    {
                        label: 'Verify On SEDO',
                        target: 'verifyOnSEDO',
                        value: 3,
                        props: {
                            'bind:domainRequests': domainRequests,
                            'bind:currentDomainRequest': currentDomainRequest,
                            'bind:spinners': spinners,
                            'bind:form': form,
                        }
                    },
                ]}

                let:activeTabValue
            >
                {#if activeTabValue === 1}

                    <TabItem id="register">
                        <RegisterDomainsPanel
                            bind:domainRequests={domainRequests}
                            bind:currentDomainRequest={currentDomainRequest}
                            bind:spinners={spinners}
                            bind:form={form}
                        />
                    </TabItem>

                {:else if activeTabValue === 2}

                    <TabItem id="addToSEDO">
                        <AddToSEDOPanel
                            bind:sedoLanguages={sedoLanguages}
                            bind:sedoAccounts={sedoAccounts}
                            bind:sedoCategories={sedoCategories}
                            bind:currentSedoAccountId={currentSedoAccountId}
                            bind:domainRequests={domainRequests}
                            bind:currentDomainRequest={currentDomainRequest}
                            bind:spinners={spinners}
                            bind:form={form}
                        />
                    </TabItem>

                {:else if activeTabValue === 3}

                    <TabItem id="verifyOnSEDO">
                        <VerifyOnSEDOPanel
                            bind:sedoLanguages={sedoLanguages}
                            bind:sedoAccounts={sedoAccounts}
                            bind:sedoCategories={sedoCategories}
                            bind:currentSedoAccountId={currentSedoAccountId}
                            bind:domainRequests={domainRequests}
                            bind:currentDomainRequest={currentDomainRequest}
                            bind:spinners={spinners}
                            bind:form={form}
                        />
                    </TabItem>

                {/if}
            </Tabs>
        </div>

    </div>
{:else}
    No Domains In This Request, Click Another
{/if}
