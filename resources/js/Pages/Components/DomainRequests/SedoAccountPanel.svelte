<script>
    import { slide } from 'svelte/transition';
    import RegisterDomainsPanel from "./RegisterDomainsPanel.svelte";
    import Tabs from "../Tabs/Tabs.svelte";
    import AddToSEDOPanel from "./AddToSEDOPanel.svelte";
    import TabItem from "../Tabs/TabItem.svelte";
    import VerifyOnSEDOPanel from "./VerifyOnSEDOPanel.svelte";
    import {TABS_VALUES} from "../../Enums/tabsValue";

    export let domainRequests = []
    export let form = {}
    export let currentDomainRequest = null
    export let currentSedoAccountId = null
    export let sedoAccounts = []
    export let sedoCategories = []
    export let sedoLanguages = []
    export let spinners = []

    let tabs = [
        {
            label: 'Register',
            value: TABS_VALUES.REGISTER_TAB,
        },
        {
            label: 'Add to SEDO',
            value: TABS_VALUES.ADD_TO_SEDO_TAB,
        },
        {
            label: 'Verify On SEDO',
            value: TABS_VALUES.VERIFY_ON_SEDO_TAB,
        },
    ]

    export let startingTabValue = TABS_VALUES.REGISTER_TAB // Starting tab

</script>

{#if currentDomainRequest !== null}

    <div class="row mb-3 p-4" transition:slide>

        <div class="row mb-3 fw-bold">
            <Tabs items={tabs} let:activeTabValue bind:activeTabValue={startingTabValue}>
                {#if activeTabValue === TABS_VALUES.REGISTER_TAB}

                    <TabItem id="register">
                        <RegisterDomainsPanel
                            bind:domainRequests={domainRequests}
                            bind:currentDomainRequest={currentDomainRequest}
                            bind:spinners={spinners}
                            bind:form={form}
                        />
                    </TabItem>

                {:else if activeTabValue === TABS_VALUES.ADD_TO_SEDO_TAB}

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

                {:else if activeTabValue === TABS_VALUES.VERIFY_ON_SEDO_TAB}

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
