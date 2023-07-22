<template>
    <section class="sg-blog-section" v-if="lengthCounter(countBlog)>0">
        <div class="container">
            <div  class="title justify-content-between">
                <h1>{{ lang.latest_news}}</h1>
            </div>
            <VueSlickCarousel  v-bind="slick_settings" :rtl="settings.text_direction == 'rtl'">
                <div v-for="(blog,index) in blogs" :key="index" class="blog-pad">
                    <div class="post">
                        <div class="entry-header">
                            <div class="entry-thumbnail">
                                <a :href="getUrl('blog/'+blog.slug)" @click.prevent="routerNavigator('blog.details',blog.slug)">
                                    <img v-lazy="blog.thumbnail" :alt="blog.title" class="img-fluid">
                                </a>
                            </div>
                            <div class="entry-content">
                                <h2 class="entry-title text-ellipse"><router-link :to="{ name: 'blog.details',params : {blogId : blog.id,slug : blog.slug} }">
                                    {{ blog.title}}</router-link></h2>
                                <p class="text-ellipse">{{ blog.short_description}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </VueSlickCarousel>
        </div><!-- /.container -->
    </section><!-- /.sg-blog-section -->
    <section class="sg-blog-section" v-else-if="shimmer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-3" v-for="(count,index) in 4" :key="index">
                    <div class="post">
                        <div class="entry-header">
                            <div class="entry-thumbnail">
                               <shimmer :height="300"></shimmer>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import shimmer from "../partials/shimmer";
import VueSlickCarousel from 'vue-slick-carousel'


export default {
    name: "blog",
    components: {shimmer,VueSlickCarousel},
    data:()=>({
        slick_settings : {
            "dots": false,
            "edgeFriction": 0.35,
            "infinite": true,
            "arrows": false,
            "autoplay": false,
            "slidesToShow": 4,
            "slidesToScroll": 4,
            "adaptiveHeight": true,
            "centerPadding": '50px',
            "responsive": [
                {
                    "breakpoint": 1024,
                    "settings": {
                        "slidesToShow": 3,
                        "slidesToScroll": 3,
                    }
                },
                {
                    "breakpoint": 768,
                    "settings": {
                        "slidesToShow": 2,
                        "slidesToScroll": 2,
                    }
                },
                {

                    "breakpoint": 480,
                    "settings": {
                        "slidesToShow": 1,
                        "slidesToScroll": 1
                    }
                },
                {

                    "breakpoint": 320,
                    "settings": {
                        "slidesToShow": 1,
                        "slidesToScroll": 1
                    }
                }
            ]
        }
    }),

    props: ['blogs'],
    watch : {
        homeResponse()
        {
            this.$store.commit('setShimmer',0);
        }
    },
    computed : {
        countBlog()
        {
            if (this.blogs && this.blogs.length > 0)
            {
                return this.blogs;
            }
            else{
                return [];
            }
        }
    }
}
</script>
