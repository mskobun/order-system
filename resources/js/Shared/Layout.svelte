<script>
    import { page, router, Link } from "@inertiajs/svelte";
    import {
        Button,
        Dropdown,
        DropdownItem,
        DropdownDivider,
        Indicator,
        Navbar,
        NavBrand,
        Input,
    } from "flowbite-svelte";
    import {
        UserSolid,
        ChevronDownOutline,
        SearchOutline,
        CartOutline,
    } from "flowbite-svelte-icons";

    export let cartCount;

    export let openCart = () => {
        router.get("/", { openCart: true });
    };

    // TODO: Remove placeholders
    const login = () => {};
    const logout = () => {};
</script>

<div class="sticky top-0 shadow-sm">
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
            <div>
                {#if $page.props.user}
                    <Button pill color="alternative">
                        <UserSolid />
                        <ChevronDownOutline />
                    </Button>
                    <Dropdown>
                        <DropdownItem class="font-bold"
                            >{$page.props.user.name}</DropdownItem
                        >
                        <DropdownDivider />
                        <DropdownItem on:click={logout}>Log out</DropdownItem>
                    </Dropdown>
                {:else}
                    <Button on:click={login}>Log In</Button>
                {/if}
            </div>
        </div>
    </Navbar>
</div>
<slot />
