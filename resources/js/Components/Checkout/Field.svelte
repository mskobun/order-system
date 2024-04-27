<script>
    import { Label, Input, Helper } from "flowbite-svelte";
    export let value;
    export let label;
    export let error;
    $: color = error ? "red" : "base";
    export let id;
    export let type = "text";
</script>

<Label for={id} {color} class="m-1">{label}</Label>
<Input {id} {type} {color} class="m-1" bind:value />
{#if error}
    <Helper class="m-1" color="red">
        {error}
    </Helper>
{/if}
