import heroImage1 from "@/assets/hero-image-1.jpg";
import heroImage2 from "@/assets/hero-image-2.jpg";

const Hero = () => {
  return (
    <section className="section-padding">
      <div className="container-custom">
        <div className="grid md:grid-cols-2 gap-12 md:gap-16 items-center">
          {/* Left Column - Text */}
          <div className="space-y-6">
            <h1 className="text-5xl md:text-6xl lg:text-7xl font-bold leading-[1.1] tracking-tight">
              We transform ideas into visual stories.
            </h1>
            <p className="text-xl md:text-2xl text-muted-foreground">
              Creative direction meets strategic thinking
            </p>
            <p className="text-base text-muted-foreground max-w-lg">
              A multidisciplinary studio crafting meaningful brand experiences through thoughtful design, compelling narratives, and innovative solutions.
            </p>
          </div>

          {/* Right Column - Image Stack */}
          <div className="relative">
            <div className="space-y-4">
              <div className="relative overflow-hidden rounded-lg aspect-[3/4]">
                <img
                  src={heroImage1}
                  alt="Creative work showcase"
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="relative overflow-hidden rounded-lg aspect-[3/4] md:ml-12">
                <img
                  src={heroImage2}
                  alt="Studio workspace"
                  className="w-full h-full object-cover"
                />
              </div>
            </div>
            <p className="text-xs text-label mt-4 md:ml-12">
              Creative Direction · 2024
            </p>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Hero;
