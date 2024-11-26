<template>
    <div class="checkbox-container">
        <input type="checkbox" id="checkbox" name="checkbox" class="checkbox" />
        <label for="checkbox" class="form-control">
            <slot></slot>
        </label>
    </div>
</template>


<script>
export default {
    name: "Checkbox"
}
</script>

<style scoped>
.checkbox-container {
    display: flex;
    align-items: center;
    /* Zentriert die Checkbox und das Label vertikal */
}

.checkbox {
    -webkit-appearance: none;
    appearance: none;
    background-color: #fff;
    margin: 0;
    font: inherit;
    color: currentColor;
    width: 1.15em;
    height: 1.15em;
    border: 0.15em solid currentColor;
    border-radius: 0.15em;
    transform: translateY(-0.075em);
    display: flex;
    justify-content: center;
    align-items: center;
}

.checkbox::before {
    content: "";
    width: 0.65em;
    height: 0.65em;
    transform: scale(0);
    transition: 120ms transform ease-in-out;
    box-shadow: inset 1em 1em black;
    background-color: CanvasText;
    transform-origin: bottom left;
    clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
}

.checkbox:checked::before {
    transform: scale(1);
}

.form-control {
    margin-left: 10px;
    /* Abstand zwischen Checkbox und Label */
}
</style>
