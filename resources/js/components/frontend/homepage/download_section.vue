<template>
    <section class="subscribe-section-2 mb-5" v-if="download_section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-sm-12">
                    <div class="subscribe-image" v-if="download_section.banner">
                        <img
                            v-lazy="download_section.banner"
                            :alt="download_section.text"
                            class="img-fluid"
                        />
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="subscribe-content">
                        <div class="header-title">
                            <h3>
                                <a>{{ download_section.text }}</a>
                            </h3>
                            <p>{{ download_section.sub_text }}</p>
                        </div>
                        <ul class="global-list justify-content-lg-start">
                            <li>
                                <a :href="download_section.play_store"
                                    ><img
                                        v-lazy="defaultAssets.google_play"
                                        :alt="download_section.text"
                                        class="img-fluid"
                                /></a>
                            </li>
                            <li>
                                <a :href="download_section.apple_store"
                                    ><img
                                        v-lazy="defaultAssets.apple_store"
                                        :alt="download_section.text"
                                        class="img-fluid"
                                /></a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.subscribe-content -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>
</template>

<script>
import shimmer from "../partials/shimmer";
export default {
    name: "download-section",
    props: ["download_section"],
    components: {
        shimmer,
    },
    data() {
        return {};
    },
    mounted() {
      console.log(this.download_section);
    },
};
</script>
