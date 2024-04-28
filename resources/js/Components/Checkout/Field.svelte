<script>
    import { Label, Input, Helper } from "flowbite-svelte";
    export let value;
    export let label;
    export let error;
    export let errorText = error;
    $: color = error ? "red" : "base";
    export let id;
    export let type = "text";
    export let placeholder;
    export let required = true;
</script>

<div>
    <Label for={id} {color} class="">{label}</Label>
    <Input {id} {type} {color} {placeholder} class="m-1" bind:value {required}/>
    {#if error}
        <Helper class="m-1" color="red">
            {errorText} 
            <slot></slot>
        </Helper>
    {/if}
</div>
