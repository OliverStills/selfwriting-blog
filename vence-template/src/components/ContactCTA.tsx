import { Button } from "@/components/ui/button";

const ContactCTA = () => {
  const scrollToContact = () => {
    const element = document.querySelector("#contact");
    element?.scrollIntoView({ behavior: "smooth" });
  };

  return (
    <section className="section-padding bg-card/30">
      <div className="container-custom">
        <div className="max-w-2xl mx-auto text-center space-y-6">
          <h2 className="text-3xl md:text-4xl font-bold">
            Have a project in mind?
          </h2>
          <p className="text-lg text-muted-foreground">
            Let's work together to bring your vision to life.
          </p>
          <Button
            size="lg"
            className="bg-primary text-primary-foreground hover:bg-primary/90"
            onClick={scrollToContact}
          >
            Start a project
          </Button>
        </div>
      </div>
    </section>
  );
};

export default ContactCTA;
