<!-- A set of password entry fields for use on the Profile page, Login page and Signup page -->
<script>
    import Field from "./Field.svelte";
    export let values = {};
    export let errors = {};
    export let showOldPassword = false;
    export let required = false;

    export let className;
</script>

<div class={className}>
    {#if showOldPassword}
        <Field
            id="current_password"
            label="Current Password"
            placeholder="••••••••"
            bind:value={values.oldPassword}
            error={errors.passwordWrongError}
            errorText={"Incorrect password!"}
            type="password"
            {required}
        ></Field>
    {/if}
    <Field
        id="password"
        label="Password"
        placeholder="••••••••"
        bind:value={values.newPassword}
        error={errors.passwordRegexError || errors.passwordLengthError}
        errorText={""}
        type="password"
        {required}
    >
        {#if errors.passwordRegexError}
            <p>
                Your password must contain at least 3 of the following
                categories:
                <br />
                - Uppercase characters
                <br />
                - Lowercase characters
                <br />
                - Digits
                <br />
                - Special characters
                <br />
                - Unicode characters
            </p>
        {/if}
        {#if errors.passwordLengthError}
            <p>
                {errors.passwordLengthError}
            </p>
        {/if}
    </Field>
    <Field
        id="confirm-password"
        label="Confirm Password"
        placeholder="••••••••"
        bind:value={values.passwordConfirmation}
        error={errors.passwordMatchError}
        errorText={errors.passwordMatchError}
        type="password"
        {required}
    ></Field>
</div>
