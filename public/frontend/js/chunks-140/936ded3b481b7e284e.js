"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[93],{2093:(t,e,s)=>{s.r(e),s.d(e,{default:()=>n});const i={name:"about",components:{shimmer:s(7982).Z},data:function(){return{slug:this.$route.params.slug}},watch:{$route:function(){this.page.link!=this.$route.params.slug&&this.$store.dispatch("othersPage",this.$route.params.slug)},page:function(){document.title=this.page.title}},mounted:function(){this.page.link!=this.$route.params.slug&&this.$store.dispatch("othersPage",this.slug)},computed:{page:function(){return this.$store.getters.getPageData},shimmer:function(){return this.$store.state.module.shimmer}}};const n=(0,s(1900).Z)(i,(function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"sg-page-content"},[s("section",{staticClass:"about-section"},[t.lengthCounter(t.page)>0?s("div",{staticClass:"container"},[s("div",{staticClass:"page-title"},[s("h1",[t._v(t._s(t.page.title))])]),t._v(" "),s("div",{domProps:{innerHTML:t._s(t.page.content)}})]):t.shimmer?t._l(3,(function(t,e){return s("div",{staticClass:"container mb-3"},[s("shimmer",{attrs:{height:200}})],1)})):t._e()],2)])}),[],!1,null,null,null).exports},7982:(t,e,s)=>{s.d(e,{Z:()=>n});const i={name:"shimmer.vue",props:["height"],data:function(){return{style:{height:this.height+"px"}}}};const n=(0,s(1900).Z)(i,(function(){var t=this,e=t.$createElement;return(t._self._c||e)("img",{staticClass:"shimmer",style:[t.height?t.style:null],attrs:{src:t.defaultAssets.shimmer,alt:"shimmer"}})}),[],!1,null,null,null).exports}}]);