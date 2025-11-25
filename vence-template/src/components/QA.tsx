import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";

const faqs = [
  {
    question: "What services do you offer?",
    answer:
      "We specialize in creative direction, brand campaigns, product photography, editorial content, and web experiences. Our approach is collaborative and tailored to each client's unique needs and goals.",
  },
  {
    question: "How long does a typical project take?",
    answer:
      "Project timelines vary based on scope and complexity. A brand campaign typically takes 8-12 weeks from initial discovery to final delivery, while smaller projects like product photography can be completed in 2-4 weeks.",
  },
  {
    question: "What is your creative process?",
    answer:
      "We follow a three-phase process: Discovery (understanding your brand and goals), Strategy (developing a creative roadmap), and Execution (bringing ideas to life with meticulous attention to detail).",
  },
  {
    question: "Do you work with international clients?",
    answer:
      "Absolutely. We work with clients around the world and are experienced in remote collaboration. We use modern tools to ensure seamless communication and project management regardless of location.",
  },
  {
    question: "What makes your studio different?",
    answer:
      "We combine strategic thinking with creative excellence. Our multidisciplinary team brings diverse perspectives, and we're committed to creating work that's not just beautiful, but meaningful and effective for your business.",
  },
  {
    question: "How do you price your services?",
    answer:
      "Each project is unique, so we develop custom proposals based on scope, timeline, and deliverables. We offer transparent pricing and work within various budget ranges to find the right solution for your needs.",
  },
];

const QA = () => {
  return (
    <section id="qa" className="section-padding">
      <div className="container-custom">
        <div className="mb-12 md:mb-16">
          <h2 className="text-4xl md:text-5xl font-bold mb-4">Q&A</h2>
          <p className="text-muted-foreground text-lg">
            Answers to common questions about our process and services.
          </p>
        </div>

        <div className="max-w-3xl">
          <Accordion type="single" collapsible className="space-y-4">
            {faqs.map((faq, index) => (
              <AccordionItem
                key={index}
                value={`item-${index}`}
                className="border border-border rounded-lg px-6"
              >
                <AccordionTrigger className="text-left text-lg font-semibold hover:no-underline py-6">
                  {faq.question}
                </AccordionTrigger>
                <AccordionContent className="text-muted-foreground pb-6">
                  {faq.answer}
                </AccordionContent>
              </AccordionItem>
            ))}
          </Accordion>
        </div>
      </div>
    </section>
  );
};

export default QA;
