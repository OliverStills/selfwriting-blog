const expertise = [
  "Creative Direction",
  "Brand Campaigns",
  "Product Photography",
  "Editorial Content",
  "Web Experiences",
  "Art Direction",
  "Visual Identity",
  "Content Strategy",
];

const About = () => {
  return (
    <section id="about" className="section-padding">
      <div className="container-custom">
        <div className="mb-12 md:mb-16">
          <h2 className="text-4xl md:text-5xl font-bold">Studio</h2>
        </div>

        <div className="grid md:grid-cols-2 gap-12 md:gap-16">
          {/* Left Column - Story */}
          <div className="space-y-6">
            <p className="text-lg text-muted-foreground leading-relaxed">
              We're a creative studio founded on the belief that great work comes from the intersection of strategy and artistry. Since 2018, we've partnered with ambitious brands to craft visual stories that resonate.
            </p>
            <p className="text-lg text-muted-foreground leading-relaxed">
              Our team brings together diverse perspectives and deep expertise, working collaboratively to push boundaries while staying grounded in what matters most—your goals.
            </p>
            <p className="text-lg text-muted-foreground leading-relaxed">
              Every project is an opportunity to create something meaningful. We approach our work with curiosity, rigor, and a commitment to excellence that shows in every detail.
            </p>
          </div>

          {/* Right Column - Expertise */}
          <div>
            <h3 className="text-2xl font-semibold mb-6">Expertise</h3>
            <ul className="space-y-4">
              {expertise.map((item) => (
                <li
                  key={item}
                  className="text-lg text-muted-foreground border-b border-border pb-4"
                >
                  {item}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>
    </section>
  );
};

export default About;
