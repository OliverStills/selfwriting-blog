import Header from "@/components/Header";
import Hero from "@/components/Hero";
import SelectedWork from "@/components/SelectedWork";
import Process from "@/components/Process";
import About from "@/components/About";
import Testimonial from "@/components/Testimonial";
import QA from "@/components/QA";
import ContactCTA from "@/components/ContactCTA";
import Contact from "@/components/Contact";
import Footer from "@/components/Footer";

const Index = () => {
  return (
    <div className="min-h-screen">
      <Header />
      <Hero />
      <SelectedWork />
      <Process />
      <About />
      <Testimonial />
      <QA />
      <ContactCTA />
      <Contact />
      <Footer />
    </div>
  );
};

export default Index;
