<script>
    import { inertia, Link } from "@inertiajs/svelte";
    import Layout from "../Shared/Layout.svelte";
    import { Card, Button } from "flowbite-svelte";

    export let menu;
    const addItem = (id) => {
        console.log(id);
    };
</script>

<Layout>
    <div class="bg-zinc-50 h-screen">
        <div class="m-5 max-w-screen-xl m-auto">
            {#each menu as { name: category, items }}
                <h1 class="font-bold text-3xl my-5">{category}</h1>
                <div class="md:grid md:grid-cols-3 md:gap-3">
                    {#each items as item}
                        <div
                            class="md:shadow-md border-b-2 p-1 flex gap-3 bg-white"
                            padding="none"
                        >
                            <img
                                class="h-20"
                                src={item.image_url}
                                alt={item.name}
                            />
                            <h2 class="text-lg text-black">
                                {item.name}
                            </h2>
                            <div class="mr-1 ml-auto">
                                <h2 class="font-bold text-l">
                                    {item.price.toFixed(2)}
                                </h2>
                                <button
                                    on:click={() => addItem(item.id)}
                                    class="text-4xl p-2 text-black"
                                >
                                    +
                                </button>
                            </div>
                        </div>
                    {/each}
                </div>
            {/each}
        </div>
    </div>
</Layout>
