<script>

    import {inertia, page} from '@inertiajs/inertia-svelte'

    let {
        pagination = null,
    } = $page.props

    export let links = {}

    const currentPage = pagination.page ? pagination.page : 1

    const count = Object.keys(links).length
    links = Object.entries(links).map(x => x[1])

    console.log('Pagination links: ', {links, count, currentPage, pagination, 'page': !!pagination.page})

    const getLink = (currentPage) => {
        if(currentPage >= count) {
            currentPage = count
        }

        if(currentPage <= 1) {
            currentPage = 1
        }

        console.log('currentPage: ', currentPage)

        return links[currentPage - 1]
    }
</script>

{#if count > 1}
    <div class="mx-auto d-flex justify-content-center align-items-center">
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item">
                    <a class="page-link" href={getLink((currentPage - 1))} aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                {#each links as link, i}
                    <li class="page-item"><a class="page-link" href={link}>{i + 1}</a></li>
                {/each}
                <li class="page-item">
                    <a class="page-link" href={getLink((currentPage + 1))} aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
{/if}

<style>
    a.page-link {
        color: #ec57d8;
    }
</style>
