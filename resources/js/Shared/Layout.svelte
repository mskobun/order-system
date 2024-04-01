<script>
    import { page } from "@inertiajs/svelte";

    import {
        Button,
        Dropdown,
        DropdownItem,
        DropdownDivider,
    } from "flowbite-svelte";

    import { Navbar, NavBrand, Input } from "flowbite-svelte";

    import {
        UserSolid,
        ChevronDownOutline,
        SearchOutline,
    } from "flowbite-svelte-icons";

    // TODO: Remove placeholders
    const login = () => {
    };
    const logout = () => {
    };
</script>

<div class="sticky top-0 shadow-sm">
    <Navbar>
        <NavBrand href="/">
            <img class="h-10" src="/storage/mcd.png" alt="Restaurant logo" />
        </NavBrand>
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
        <div class="h-10">
            {#if $page.props.user}
                <Button pill color="alternative">
                    <UserSolid />
                    <ChevronDownOutline />
                </Button>
                <Dropdown>
                    <DropdownItem class="font-bold">{$page.props.user.name}</DropdownItem>
                    <DropdownDivider />
                    <DropdownItem on:click={logout}>Log out</DropdownItem>
                </Dropdown>
            {:else}
                <Button on:click={login}>Log In</Button>
            {/if}
        </div>
    </Navbar>
</div>
<slot />
