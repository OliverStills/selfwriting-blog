const Testimonial = () => {
  return (
    <section className="section-padding bg-card/30">
      <div className="container-custom">
        <div className="max-w-4xl mx-auto text-center space-y-8">
          <blockquote className="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">
            "Working with this team changed the way we think about our brand. Their strategic vision and creative execution exceeded every expectation."
          </blockquote>
          <div className="flex items-center justify-center gap-4">
            <div className="text-center">
              <p className="font-semibold">Alex Rivera</p>
              <p className="text-sm text-label">Founder at Northline</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Testimonial;
