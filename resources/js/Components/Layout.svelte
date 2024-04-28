<script>
    import { page, router, Link } from "@inertiajs/svelte";
    import {
        Button,
        Dropdown,
        DropdownItem,
        DropdownDivider,
        Indicator,
        Navbar,
        Banner,
        NavBrand,
        Input,
    } from "flowbite-svelte";
    import {
        UserSolid,
        ChevronDownOutline,
        SearchOutline,
        CartOutline,
        InfoCircleSolid,
    } from "flowbite-svelte-icons";

    export let cartCount;
    export let displayCartButton = true;

    export let openCart = () => {
        router.get("/", { open_cart: true });
    };

    // Note: added some functionality
    // for now, the login form will just lead to the default page (currently /)
    function login() {
        router.get("/login");
    }

    function logout() {
        // this took a while to figure out... I think it's hacky to just reload it directly but oh well
        router.post("/logout", undefined, {
            onSuccess: () => window.location.reload(),
        });
    }
</script>

<div class="sticky top-0 shadow-sm">
    {#if !$page.props.user}
        <Banner dismissable={false}>
            <InfoCircleSolid class="mt-0.5" />
            Please log in or sign up to order from the restaurant
        </Banner>
    {/if}
    <Navbar>
        <Link href="/">
            <NavBrand>
                <img
                    class="h-10"
                    src="/storage/mcd.png"
                    alt="Restaurant logo"
                />
            </NavBrand>
        </Link>
        <!--
        <div class="hidden relative md:block">
            <div
                class="flex absolute inset-y-0 start-0 items-center ps-3 pointer-events-none"
            >
                <SearchOutline class="w-4 h-4" />
            </div>
            <Input id="search-navbar" class="ps-10" placeholder="Search..." />
        </div>
        -->
        <div class="flex gap-1 h-10">
            {#if $page.props.user && displayCartButton}
                <Button outline class="relative" on:click={() => openCart()}>
                    <CartOutline />
                    {#if cartCount > 0}
                        <Indicator
                            color="red"
                            border
                            size="xl"
                            placement="top-right"
                        >
                            <span class="text-white text-xs font-bold"
                                >{cartCount}</span
                            >
                        </Indicator>
                    {/if}
                </Button>
            {/if}
            <div>
                {#if $page.props.user}
                    <Button pill color="alternative">
                        <UserSolid />
                        <ChevronDownOutline />
                    </Button>
                    <Dropdown>
                        <div slot="header" class="px-4 p-2">
                            <div class="font-bold text-sm">{$page.props.user.name} </div>
                            <div class="text-sm"> {$page.props.user.email} </div>
                        </div>
                        <Link href="/profile">
                            <DropdownItem>Edit Profile</DropdownItem>
                        </Link>

                        <Link href="/order/list">
                            <DropdownItem>My Orders</DropdownItem>
                        </Link>

                        <DropdownItem on:click={logout}>Log out</DropdownItem>
                    </Dropdown>
                {:else}
                    <Link href="/login">
                        <Button color="alternative">Log in</Button>
                    </Link>
                    <Link href="/signup">
                        <Button on:click={login}>Sign up</Button>
                    </Link>
                {/if}
            </div>
        </div>
    </Navbar>
</div>
<slot />
