<script>
    import { inertia, Link, router } from "@inertiajs/svelte";
    import Layout from "../Shared/Layout.svelte";
    import MenuItem from "../Shared/MenuItem.svelte";

    export let menu;
    export let items;
    export let cart = {};
    const modifyCart = (id, addAmount) => {
        cart[id] = (cart[id] || 0) + addAmount;
        router.post("/cart/modify", { id, amount: cart[id] });

        if (cart[id] <= 0) {
            delete cart[id];
            // For svelte to trigger a re-render
            cart = cart;
            router.post(
                "/cart/modify",
                { id, amount: 0 },
                { preserveScroll: true },
            );
        } else {
            router.post(
                "/cart/modify",
                { id, amount: cart[id] },
                { preserveScroll: true },
            );
        }
    };
</script>

<Drawer
    transitionType="fly"
    {transitionParams}
    bind:hidden={cartDrawerHidden}
    id="sidebar1"
    placement="right"
    width="w-full md:w-96"
>
    <div class="flex items-center">
        <h1 class="text-3xl font-bold">Cart</h1>
        <CloseButton
            on:click={() => (cartDrawerHidden = true)}
            class="dark:text-white"
        />
    </div>
    <div></div>
</Drawer>

<Layout {cartCount} openCart={() => (cartDrawerHidden = false)}>
    <div class="bg-zinc-50 h-screen">
        <div class="m-5 max-w-screen-xl m-auto">
            {#each menu as { name: category, items: category_items }}
                <h1 class="font-bold text-3xl py-3">{category}</h1>
                <div class="md:grid md:grid-cols-3 md:gap-3 max-md:divide-y">
                    {#each category_items as id}
                        <MenuItem
                            name={items[id].name}
                            image_url={items[id].image_url}
                            price={items[id].price}
                            amount={cart[items[id].id] || 0}
                            modifyAmount={(addAmount) =>
                                modifyCart(id, addAmount)}
                            sideStripe
                        />
                    {/each}
                </div>
            {/each}
        </div>
    </div>
</Layout>
