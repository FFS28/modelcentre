<template>
    <div v-if="customInfo.length > 0">
        <h3 class="border-bottom">Customise Options</h3>
        <input type="hidden" name="custom_price" :value="custom_price" />
        <accordian v-for="(customInfoItems, index) in customInfo" :title="'customInfoItems-'+index" :key="customInfoItems.cus_info.title" :active="true">
            <div slot="header">
                <h3 class="no-margin display-inbl" v-if="customInfoItems.cus_info.type == 'radio' || (customInfoItems.cus_info.price * 1) == 0">
                    {{ customInfoItems.cus_info.title }}
                </h3>
                <h3 class="no-margin display-inbl" v-else>
                    {{ customInfoItems.cus_info.title }} + {{ customInfoItems.cus_info.price }}
                </h3>

                <i class="rango-arrow"></i>
            </div>

            <div slot="body">
                <div class="full-short-description">
                    <div v-if="customInfoItems.cus_info.type == 'radio'" class="row no-margin">
                        <div v-for="(customSubInfoItems, index) in customInfoItems.cus_subinfo" :key="'customSubInfoItems' + index" class="custom-info-box mb-1">
                            <button type="button" class="mycss" :curvalue="customSubInfoItems.type_price" v-on:click="onSelectHandler" :title="customSubInfoItems.type_title + '+' + parseFloat(customSubInfoItems.type_price).toFixed(2)">
                                <div style="text-overflow:ellipsis; overflow:hidden; display: -webkit-box !important; -webkit-line-clamp: 3; -webkit-box-orient: vertical; white-space: normal;">
                                    {{customSubInfoItems.type_title}}<br>+{{parseFloat(customSubInfoItems.type_price).toFixed(2)}}
                                </div>
                            </button>
                        </div>
                        <div v-if="upload">
                            <input type="file" id="actual-btn" v-on:change="selectFile" hidden/>
                            <label class="file_label" for="actual-btn">Choose File</label>
                            <span id="file-chosen">No file selected</span>
                        </div>
                    </div>
                    <div v-else-if="customInfoItems.cus_info.type == 'area'">
                        <div class="md-form">
                            <textarea id="form7" class="md-textarea form-control" rows="3" :curvalue="customInfoItems.cus_info.price" v-on:change="change_value"></textarea>
                        </div>
                    </div>
                    <div v-else-if="customInfoItems.cus_info.type == 'field'">
                        <input type="number" id="exampleForm2" class="form-control" :curvalue="customInfoItems.cus_info.price" v-on:change="change_value">
                    </div>
                </div>
            </div>
        </accordian>
    </div>
</template>

<script>
    export default {
        props: ["product", "token"],
        data: function () {
            return {
                customInfo : [],
                upload : false,
                custom_price : 0
            }
        },
        methods: {
            onSelectHandler(event) {

                let del_price = 0;
                this.upload = false;

                const slelements = event.target.parentNode.parentNode.parentNode.children;

                if(event.target.parentNode.className.split(" ").find( x => x == 'mycss-active')) {
                    del_price -= Math.round(event.target.parentNode.getAttribute('curvalue') * 100) / 100;
                    event.target.parentNode.classList.remove('mycss-active');
                }else {
                    for (let i = 0;i < slelements.length;i ++) {
                        if(slelements[i].children[0].className.indexOf('mycss-active') != -1){
                            del_price -= Math.round(slelements[i].children[0].getAttribute('curvalue') * 100) / 100;
                            slelements[i].children[0].classList.remove('mycss-active')
                        }
                    }
                    del_price += Math.round(event.target.parentNode.getAttribute('curvalue') * 100) / 100;
                    event.target.parentNode.classList.add('mycss-active')
                    if(event.target.innerText.indexOf('Deluxe') == 0)
                    {
                        this.upload = true;
                    }
                }
                this.custom_price = del_price;

                Array.prototype.forEach.call(document.getElementsByClassName('product-price'), (element, index)=>{
                    if(index == 0)
                        Array.prototype.forEach.call(element.getElementsByTagName('span'), price_element => {
                            let result = (parseFloat(price_element.textContent.match(/\d+(\.\d+)?/g)) + del_price ).toFixed(2);
                            price_element.textContent = price_element.textContent = price_element.textContent.replaceAll(/\d+(\.\d+)?/g, "" + result );
                        })
                    else
                        Array.prototype.forEach.call(element.getElementsByTagName('span'), price_element => {
                            let count = document.getElementById("quantity-changer").value;
                            let result = ( parseFloat(price_element.textContent.match(/\d+(\.\d+)?/g)) + del_price * count).toFixed(2);
                            if(price_element.textContent.match(/(NaN)/g))
                                price_element.textContent = price_element.textContent.replaceAll(/(NaN)/g, result.toString());
                            else
                                price_element.textContent = price_element.textContent.replaceAll(/\d+(\.\d+)?/g, result.toString())
                        })
                })
            },
            selectFile(event){
                document.getElementById("file-chosen").innerText = event.target.files[0].name;
            },
            change_value(event){
                let del_price = 0;

                if(event.target.value == ""){
                    del_price -= Math.round(event.target.getAttribute('curvalue') * 100) / 100;
                }else{
                    del_price += Math.round(event.target.getAttribute('curvalue') * 100) / 100;
                }
                this.custom_price = del_price;

                Array.prototype.forEach.call(document.getElementsByClassName('product-price'), (element, index) => {
                    if(index == 0)
                    Array.prototype.forEach.call(element.getElementByTagName('span'), price_element => {
                        let result = (parseFloat(price_element.textContent.match(/\d+(\.\d+)?/g)) + del_price).toFixed(2)
                        price_element.textContent = price_element.textContent.replaceAll(/\d+(\.\d+)?/g, "" + result)
                    })
                    else
                    Array.prototype.forEach.call(element.getElementByTagName('span'), price_element => {
                        let count = document.getElementById("quantity-changer").value;
                        let result = (parseFloat(price_element.textContent.match(/\d+(\.\d+)?/g)) + del_price * count ).toFixed(2);
                        if(price_element.textContent.match(/(NaN)/g))
                        price_element.textContent = price_element.textContent.replaceAll(/(NaN)/g, result.toString());
                        else
                        price_element.textContent = price_element.textContent.replaceAll(/\d+(\.\d+)?/g, result.toString());
                    })
                })
            }
        },
        created(){
            fetch("/getAllCustomizeOptions", {
                method: "post",
                body: JSON.stringify({
                    _token: this.token,
                    product_id: this.product
                }),
                headers: {
                    "Content-type": "application/json; charset=UTF-8"
                }
            }).then(res => res.json()).then(res => {
                this.customInfo = res;
            }).catch(err => {console.log(err)})
        }
    }
</script>
<style>
    .file_label {
        background-color: rgb(5 128 184);
        color: white;
        padding: 0.5rem;
        font-family: sans-serif;
        border-radius: 0.3rem;
        cursor: pointer;
        margin-top: 1rem;
    }

    #file-chosen{
        margin-left: 0.3rem;
        font-family: sans-serif;
    }
</style>
