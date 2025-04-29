        <template>
        <div class="px-4 pt-4">
            <template v-if="images.length > 0">
              <v-carousel class="rounded-lg overflow-hidden"
                          height="400"
                          hide-delimiter-background
                          show-arrows="hover"
                          cycle interval="8000"
                          delimiter-icon="mdi-circle"
                          elevation="1">
                <template v-slot:prev="{ props }">
                  <v-btn variant="elevated"
                         v-bind="props"
                         class="carousel-arrow"
                         color="blue-grey-darken-3"
                         rounded="circle">
                    <v-icon>mdi-chevron-left</v-icon>
                  </v-btn>
                </template>
                <template v-slot:next="{ props }">
                  <v-btn variant="elevated"
                         v-bind="props"
                         class="carousel-arrow"
                         color="blue-grey-darken-3"
                         rounded="circle">
                    <v-icon>mdi-chevron-right</v-icon>
                  </v-btn>
                </template>
                <v-carousel-item v-for="(image, index) in images" :key="index" :src="image" cover>
                  <template v-slot:placeholder>
                    <div class="d-flex
                                align-center
                                justify-center
                                fill-height">
                      <v-progress-circular indeterminate
                                           color="primary">
                      </v-progress-circular>
                    </div>
                  </template>
                  <div class="image-overlay"></div>
                </v-carousel-item>
              </v-carousel>
            </template>
  
            <!-- Wenn keine Bilder vorhanden sind -->
            <template v-else>
              <v-sheet class="d-flex
                              align-center
                              justify-center
                              rounded-lg
                              bg-grey-lighten-4"
                        height="250">
                <div class="text-center">
                  <v-icon size="64" color="grey-darken-1">mdi-image-off</v-icon>
                  <div class="text-body-1 text-grey-darken-1 mt-2">
                    Keine Bilder vorhanden
                  </div>
                </div>
              </v-sheet>
            </template>
          </div>

        </template>
<script>

export default {
  name: 'ImageCarousel',
  props: {
    images: {
      type: Array,
      default: () => []
    }
  }
}

</script>

<style scoped>

.image-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 100%);
}

.v-carousel :deep(.v-carousel__controls) {
  background: linear-gradient(0deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0) 100%);
  padding-bottom: 8px;
}

.v-carousel :deep(.v-carousel__controls .v-btn) {
  color: white !important;
}

@media (max-width: 600px) {
  .v-carousel {
    height: 250px !important;
  }
}



</style>