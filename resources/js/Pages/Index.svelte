<script>
    import { inertia, Link, router } from "@inertiajs/svelte";
    import Layout from "../Components/Layout.svelte";
    import MenuItem from "../Components/MenuItem.svelte";
    import { Drawer, CloseButton, Button } from "flowbite-svelte";
    import { ArrowRightOutline } from "flowbite-svelte-icons";
    import { sineIn } from "svelte/easing";

    let transitionParams = {
        x: 320,
        duration: 200,
        easing: sineIn,
    };

    export let menu;
    export let items;
    export let cart = {};
    // This sets cartDrawerHidden to value of open_cart initially
    // But then it's only set by user actions. This is so reloads due to
    // cart changes don't suddenly trigger
    export let open_cart = false;
    let cartDrawerHidden = !open_cart;
    $: cartCount = Object.entries(cart).reduce(
        (acc, [_id, amount]) => acc + amount,
        0,
    );

    $: cartTotal = Object.entries(cart).reduce(
        (acc, [id, amount]) => acc + items[id].price * amount,
        0,
    );

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
                {
                    preserveScroll: true,
                },
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
    <div class="flex flex-col h-full justify-start">
        <div class="flex items-center py-3">
            <h1 class="text-3xl font-bold">Cart</h1>
            <CloseButton
                on:click={() => (cartDrawerHidden = true)}
                class="dark:text-white"
            />
        </div>

        <div class="divide-y overflow-y-scroll">
            {#each Object.entries(cart) as [id, amount]}
                <MenuItem
                    name={items[id].name}
                    image_url={items[id].image_url}
                    price={items[id].price * cart[id]}
                    amount={cart[id] || 0}
                    modifyAmount={(addAmount) => modifyCart(id, addAmount)}
                    showDescription={false}
                />
            {/each}
        </div>
        <div class="mb-0 mt-auto">
            <div class="mx-3 mt-3 flex justify-between text-xl">
                <span> Total </span>
                <span> RM {cartTotal.toFixed(2)} </span>
            </div>
            <Link href="/order/confirm">
                <Button class="mt-3 w-full font-bold text-lg">
                    <span> Checkout </span>
                    <ArrowRightOutline class="mt-0.5" />
                </Button>
            </Link>
        </div>
    </div>
</Drawer>

<Layout {cartCount} openCart={() => (cartDrawerHidden = false)}>
    <div class="bg-zinc-50 h-screen">
        <div class="m-5 max-w-screen-xl m-auto">
            {#each menu as { name: category, items: category_items }}
                <h1 class="font-bold text-3xl py-3">{category}</h1>
                <div class="md:grid md:grid-cols-3 md:gap-3 max-md:divide-y">
                    {#each category_items as id}
                        <MenuItem
                            id={id}
                            name={items[id].name}
                            description={items[id].description}
                            image_url={items[id].image_url}
                            price={items[id].price}
                            amount={cart[id] || 0}
                            modifyAmount={(addAmount) =>
                                modifyCart(id, addAmount)}
                            sideStripe
                            isAvailable={!!items[id].available}
                            showDescription={true}
                        />
                    {/each}
                </div>
            {/each}
        </div>
    </div>
</Layout>
