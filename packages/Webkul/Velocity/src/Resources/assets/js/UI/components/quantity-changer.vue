<template>
    <div
        :class="`quantity control-group ${
            errors.has(controlName) ? 'has-error' : ''
        }`"
    >


        <div class="input-btn-group myquantity">
            <label
                class="required"
                for="quantity-changer"
                v-text="'Qty'"
                style="font-size: 16px"
            ></label>
            <!-- <button type="button" class="decrease" @click="decreaseQty()">
                <i class="rango-minus"></i>
            </button> -->

            <input
                ref="quantityChanger"
                :name="controlName"
                :model="qty"
                class="control"
                id="quantity-changer"
                v-validate="validations"
                :data-vv-as="`&quot;${quantityText}&quot;`"
                @keyup="setQty($event)"
                style="width: 70px !important; height: 60px !important; border: 1px solid black"
            />
<!--
            <button type="button" class="increase" @click="increaseQty()">
                <i class="rango-plus"></i>
            </button> -->
        </div>

        <span class="control-error" v-if="errors.has(controlName)">{{
            errors.first(controlName)
        }}</span>
    </div>
</template>

<script>
export default {
    template: '#quantity-changer-template',

    inject: ['$validator'],

    props: {
        controlName: {
            type: String,
            default: 'quantity'
        },

        quantity: {
            type: [Number, String],
            default: 1
        },

        quantityText: {
            type: String,
            default: 'Quantity'
        },

        minQuantity: {
            type: [Number, String],
            default: 1
        },

        validations: {
            type: String,
            default: 'required|numeric|min_value:1'
        }
    },

    data: function() {
        return {
            qty: this.quantity
        };
    },

    mounted: function() {
        this.$refs.quantityChanger.value = this.qty > this.minQuantity
            ? this.qty
            : this.minQuantity;
    },

    watch: {
        qty: function(val) {
            this.$refs.quantityChanger.value = ! isNaN(parseFloat(val)) ? val : 0;

            this.qty = ! isNaN(parseFloat(val)) ? this.qty : 0;

            this.$emit('onQtyUpdated', this.qty);

            this.$validator.validate();
        }
    },

    methods: {
        setQty: function({ target }) {
    	    this.qty = parseInt(target.value);
            Array.prototype.forEach.call(document.getElementsByClassName('product-price')[1].getElementsByTagName('span'), (price_element, index) => {
                let result = parseFloat(document.getElementsByClassName('product-price')[0].getElementsByTagName('span')[index].textContent.match(/\d+(\.\d+)?/g) * this.qty ).toFixed(2);
                if(price_element.textContent.match(/(NaN)/g))
                    price_element.textContent = price_element.textContent.replaceAll(/(NaN)/g, result.toString());
                else
                    price_element.textContent = price_element.textContent.replaceAll(/\d+(\.\d+)?/g, result.toString());
            })
        },

        decreaseQty: function() {
            if (this.qty > this.minQuantity) this.qty = parseInt(this.qty) - 1;
        },

        increaseQty: function() {
            this.qty = parseInt(this.qty) + 1;
        }
    }
};
</script>

<style>

.myquantity {
    display: inline-flex !important;
    justify-content: center;
    align-items: baseline;
}


</style>