<script>
    import { Popover, Button, ButtonGroup } from "flowbite-svelte";
    export let id, name, description, image_url, price, amount, modifyAmount;
    export let showDescription = true;
    export let sideStripe = false;
    export let isAvailable = true;
    export let hideButton = false;
</script>

<!-- The div is necessary here, so that the code outside the component
can set it's own border (for example as a menu item separator), and not
influence the side-stripe, which is also implemented as a border.
-->
<div id={"item-" + id}>
    <!-- Ideally, the classes should not be repeated,
we should be able to do something like:
baseClasses + (ordered) ? red border : white border
However, for some reason, if you do that,
the border-primary-600 style will not be present in
the compiled css. I guess tailwind only generates them
if it sees a usage, and the logic becomes too complicated
for it to figure it out. -->
    {#if showDescription}
        <Popover
            class="w-72 text-sm"
            placement="top-middle"
            title="Description"
            triggeredBy={"#item-" + id}
        >
            {description !== "" ? description : "None"}
        </Popover>
    {/if}
    <div
        class={(sideStripe && amount > 0
            ? "border-primary-600 border-l-4 p-1 flex md:rounded-md gap-3 bg-white"
            : "border-white border-l-4 p-1 flex md:rounded-md gap-3 bg-white") +
            (isAvailable ? "" : " grayscale opacity-90 pointer-events-none")}
    >
        <img class="h-20 md:h-24" src={image_url} alt={name} id="menuimage" />
        <h2 class="text-lg text-black">
            {name}
        </h2>
        <div class="mr-1 ml-auto flex-col flex items-end">
            <h2 class="font-bold text-l">
                {price.toFixed(2)}
            </h2>
            <div class="mb-2 mt-auto">
                {#if !hideButton}
                    {#if amount == 0}
                        <Button
                            on:click={() => modifyAmount(1)}
                            class="rounded-full w-8 h-8 p-2 inline-flex text-center text-xl"
                        >
                            +
                        </Button>
                    {:else}
                        <ButtonGroup size="xs" class="h-8">
                            <Button
                                pill
                                on:click={() => {
                                    modifyAmount(-1);
                                }}>-</Button
                            >
                            <!-- Not really a button, but I just wanted to use the ButtonGroup class -->
                            <Button pill>
                                {amount}
                            </Button>
                            <Button
                                pill
                                on:click={() => {
                                    modifyAmount(1);
                                }}
                            >
                                +
                            </Button>
                        </ButtonGroup>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
</div>
