<template>
  <div>
    <!-- FAQs-2 -->
    <section id="faqs-2" class="gr--whitesmoke pb-30 inner-page-hero faqs-section division">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-11 col-xl-10">


            <!-- INNER PAGE TITLE -->
            <div class="inner-page-title">
              <h2 class="s-52 w-700">Questions & Answers</h2>
            </div>


            <!-- QUESTIONS ACCORDION -->
            <div class="accordion-wrapper">
              <ul class="accordion">

                <!-- QUESTIONS CATEGORY #1 -->
                <li class="accordion-item is-active">

                  <!-- CATEGORY HEADER -->
                  <div class="list-group">
                    <h4 class="s-28 w-700">Getting Started</h4>
                    <hr>
                  </div>

                  <!-- QUESTIONS LOOP -->
                  <div v-for="(faq, index) in faqs" :key="index" 
                       class="accordion-panel-item mb-35"
                       :class="{ 'is-active': activeIndex === index }">

                    <!-- Question -->
                    <div class="faqs-2-question" @click="toggleFaq(index)">
                      <h5 class="s-22 w-700"><span>{{ index + 1 }}.</span> {{ faq.question }}</h5>
                    </div>

                    <!-- Answer -->
                    <div class="faqs-2-answer color--grey" v-show="activeIndex === index">
                      <p>{{ faq.answer }}</p>
                    </div>

                  </div> <!-- END QUESTION ITEM -->

                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
useHead({
  title: 'Faqs'
})

const activeIndex = ref(0)

const faqs = [
  {
    question: 'What is CODECV and how does it work?',
    answer: "CODECV provides consulting for IT professionals in general. Be it CV, LinkedIn profile and Cover Letter. Our differential is that we provide interview training based on the person's professional profile, and we also accompany the candidate until he passes the probation period."
  },
  {
    question: 'Can I make an online appointment?',
    answer: "All appointments could be done via WhatsApp, Instagram's DM or email."
  },
  {
    question: 'How can I select the best plan?',
    answer: 'The first step is to book an appointment with CODE CV and explain to us your background.'
  },
  {
    question: 'What if I have any questions after the service?',
    answer: 'Absolutely fine. Our support is 24/7 pre-sale, and post-sale.'
  }
]

const toggleFaq = (index) => {
  if (activeIndex.value === index) {
    activeIndex.value = -1
  } else {
    activeIndex.value = index
  }
}
</script>
