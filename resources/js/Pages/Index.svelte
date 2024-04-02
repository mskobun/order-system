<script>
    import { inertia, Link, router } from "@inertiajs/svelte";
    import Layout from "../Shared/Layout.svelte";
    import MenuItem from "../Shared/MenuItem.svelte";

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
                        <MenuItem
                            name={item.name}
                            image_url={item.image_url}
                            price={item.price}
                            amount={cart[item.id] || 0}
                            modifyAmount={(addAmount) =>
                                modifyCart(item.id, addAmount)}
                            sideStripe
                        />
                    {/each}
                </div>
            {/each}
        </div>
    </div>
</Layout>
