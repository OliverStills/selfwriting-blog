import { Card } from "@/components/ui/card";

const steps = [
  {
    number: "01",
    title: "Discovery",
    description:
      "We begin by deeply understanding your brand, goals, and audience. Through research and collaboration, we uncover insights that inform our creative direction.",
  },
  {
    number: "02",
    title: "Strategy",
    description:
      "With insights in hand, we craft a strategic approach that aligns creative vision with business objectives. Every decision is purposeful and data-informed.",
  },
  {
    number: "03",
    title: "Execution",
    description:
      "Our team brings ideas to life with meticulous attention to detail. From concept to delivery, we ensure every element meets the highest standards.",
  },
];

const Process = () => {
  return (
    <section id="process" className="section-padding bg-card/30">
      <div className="container-custom">
        <div className="mb-12 md:mb-16">
          <h2 className="text-4xl md:text-5xl font-bold mb-4">Process</h2>
          <p className="text-muted-foreground text-lg max-w-2xl">
            Our approach combines creative thinking with strategic methodology to deliver exceptional results.
          </p>
        </div>

        <div className="grid md:grid-cols-3 gap-8 md:gap-12">
          {steps.map((step) => (
            <Card
              key={step.number}
              className="p-8 space-y-4 bg-background border-border"
            >
              <p className="text-4xl font-bold text-label">{step.number}</p>
              <h3 className="text-2xl font-semibold">{step.title}</h3>
              <p className="text-muted-foreground leading-relaxed">
                {step.description}
              </p>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Process;
