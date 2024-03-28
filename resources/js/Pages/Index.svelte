<script>
    import { inertia, Link } from "@inertiajs/svelte";
    import Layout from "../Shared/Layout.svelte";
    import { Card, Button } from "flowbite-svelte";

    export let menu;
</script>

<Layout>
    <div class="m-5 max-w-screen-xl m-auto">
        {#each menu as { name: category, items }}
            <h1 class="font-bold text-3xl my-5">{category}</h1>
            <div class="flex flex-wrap gap-3">
                {#each items as item}
                    <Card class="grid grid-cols-3 grid-rows-2 items-center" padding="none">
                        <img class="col-span-1 row-span-2 p-4" src={item.image_url} alt={item.name} />
                        <h2 class="col-span-2 text-xl py-1 text-black font-bold"> {item.name} </h2>
                        <div class="row-start-2 row-end-3 col-start-2 col-end-4 flex justify-between items-center">
                        <h2> RM{item.price} </h2>
                        <button class="text-4xl p-2 text-black"> + </button>
                        </div>
                    </Card>
                {/each}
            </div>
        {/each}
    </div>
</Layout>
