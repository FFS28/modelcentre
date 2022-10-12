<template>
    <div class="myCategory">
        <ul>
            <li class="mydropdown" v-for="lineCategories in categories" :key="lineCategories.id">
                <a :href="rootCategoryUrl + lineCategories.url_path" class="dropbtn">{{lineCategories.name}}</a>
                <div v-if="lineCategories.children.length != 0" class="mydropdown-content">
                    <div v-for="subCategories in lineCategories.children"  :key="subCategories.id">
                        <a :href="rootCategoryUrl + subCategories.url_path" class="parent">{{subCategories.name}}</a>
                        <div v-for="maxCategories in subCategories.children" :key="maxCategories.id">
                            <a :href="rootCategoryUrl + maxCategories.url_path">{{maxCategories.name}}</a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['root'],
    data(){
        return {
            categories: [],
            rootCategoryUrl: this.root
        }
    },
    mounted(){
    },
    async created(){
        var data = await fetch("/getAllCategories").then(res => {
            return res.json().then(data => {
                return data;
            })
        }).catch(err => {console.log(err)})
        let home = {category_icon_url: null, children: [], position: 0, id: 0, image_url: null, name: "Home", slug: "home", url_path: ""}
        data[0].children.unshift(home);
        this.categories = data[0].children;
        this.rootCategoryUrl = this.rootCategoryUrl + "/";
    }
};
</script>

<style>

.myCategory ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: rgb(4 126 181);
}

.myCategory > ul li {
    float: left;
}

.myCategory > ul li a, .dropbtn {
    display: inline-block;
    color: white;
    text-align: center;
    padding: 6px 16px;
    text-decoration: none;
}

.myCategory > ul > li > a {
    text-transform: uppercase;
}

.mydropdown-content > a {
    text-transform: uppercase;
}

.myCategory > ul > li > a:hover {
    background-color: red;
    text-decoration: none;
    z-index: 99;
}

.mydropdown:hover a {
    display: block;
}

.mydropdown:hover .mydropdown-content {
    display: block;
}

.myCategory li.mydropdown {
    display: inline-block;
}

.myCategory ul li .mydropdown-content {
    display: none;
    position: absolute;
    background-color: #f9f9f9;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    width: calc(100% - 160px);
    left: -50px;
    column-count : 5;
    column-gap: 40px;
    height: fit-content;
    border-top: 5px solid red;
    padding: 40px;
    max-height: 550px;
}

.myCategory .mydropdown-content a {
    color: black;
    padding: 2px 16px;
    text-decoration: none;
    display: block;
    text-align: left;
}

.myCategory .mydropdown-content a.parent {
    background-color: none !important;
    font-weight: 700;
    font-size: 16px;
    color: rgb(33 102 168);
}

.myCategory .mydropdown-content a:hover {
    background-color: none !important;
    color: rgb(33 102 168);
}

.mydropdown-content a:hover {
    background-color: #f1f1f1;
}

.mydropdown:hover .mydropdown-content {
    display: block;
    position: absolute;
}
</style>