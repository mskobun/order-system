<script>
    import { inertia, Link, router } from "@inertiajs/svelte";
    import Layout from "../Shared/Layout.svelte";
    import { Button, ButtonGroup } from "flowbite-svelte";

    export let menu;
    export let cart = {};
    const modifyCart = (id, addAmount) => {
        cart[id] = (cart[id] || 0) + addAmount;
        router.post("/cart/modify", { id, amount: cart[id] });

        if (cart[id] <= 0) {
            delete cart[id];
            // For svelte to trigger a re-render
            cart = cart;
            router.post("/cart/modify", { id, amount: 0 });
        } else {
            router.post("/cart/modify", { id, amount: cart[id] });
        }
    };
</script>

<Layout>
    <div class="bg-zinc-50 h-screen">
        <div class="m-5 max-w-screen-xl m-auto">
            {#each menu as { name: category, items }}
                <h1 class="font-bold text-3xl py-3">{category}</h1>
                <div class="md:grid md:grid-cols-3 md:gap-3 max-md:divide-y">
                    {#each items as item}
                        <!-- The div is necessary here because we use 2 borders
                    with different colors and widths, one for dividing the items,
                    other is a side stripe to identify items in the cart.
                    So we put the former on this div, and the latter on the div
                    inside it. -->
                        <div>
                            <!-- Ideally, the classes should not be repeated,
                        we should be able to do something like:
                        baseClasses + (ordered) ? red border : white border
                        However, for some reason, if you do that,
                        the border-primary-600 style will not be present in
                        the compiled css. I guess tailwind only generates them
                        if it sees a usage, and the logic becomes too complicated
                        for it to figure it out. -->
                            <div
                                class={cart[item.id] !== undefined
                                    ? "border-primary-600 border-l-4 p-1 flex md:rounded-md gap-3 bg-white"
                                    : "border-white border-l-4 p-1 flex md:rounded-md gap-3 bg-white"}
                            >
                                <img
                                    class="h-20 w-20"
                                    src={item.image_url}
                                    alt={item.name}
                                />
                                <h2 class="text-lg text-black">
                                    {item.name}
                                </h2>
                                <div
                                    class="mr-1 ml-auto flex-col flex items-end"
                                >
                                    <h2 class="font-bold text-l">
                                        {item.price.toFixed(2)}
                                    </h2>
                                    <div class="mb-2 mt-auto">
                                        {#if cart[item.id] === undefined}
                                            <Button
                                                on:click={() =>
                                                    modifyCart(item.id, 1)}
                                                class="rounded-full w-8 h-8 p-2 inline-flex text-center text-xl"
                                            >
                                                +
                                            </Button>
                                        {:else}
                                            <ButtonGroup size="xs" class="h-8">
                                                <Button
                                                    pill
                                                    on:click={() => {
                                                        modifyCart(item.id, -1);
                                                    }}>-</Button
                                                >
                                                <!-- Not really a button, but I just wanted to use the ButtonGroup class -->
                                                <Button pill>
                                                    {cart[item.id]}
                                                </Button>
                                                <Button
                                                    pill
                                                    on:click={() => {
                                                        modifyCart(item.id, 1);
                                                    }}
                                                >
                                                    +
                                                </Button>
                                            </ButtonGroup>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/each}
                </div>
            {/each}
        </div>
    </div>
</Layout>
