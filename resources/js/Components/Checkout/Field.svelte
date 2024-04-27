<script>
    import { Label, Input, Helper } from "flowbite-svelte";
    export let value;
    export let label;
    export let error;
    export let errorText;
    $: color = error ? "red" : "base";
    export let id;
    export let type = "text";
    export let placeholder;
    export let required = true;
</script>

<Label for={id} {color} class="m-1">{label}</Label>
<Input {id} {type} {color} {placeholder} class="m-1" bind:value {required}/>
{#if error}
    <Helper class="m-1" color="red">
        {errorText} 
        <slot></slot>
    </Helper>
{/if}
