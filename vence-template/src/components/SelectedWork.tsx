import { Card } from "@/components/ui/card";
import project1 from "@/assets/project-1.jpg";
import project2 from "@/assets/project-2.jpg";
import project3 from "@/assets/project-3.jpg";
import project4 from "@/assets/project-4.jpg";
import project5 from "@/assets/project-5.jpg";
import project6 from "@/assets/project-6.jpg";

const projects = [
  {
    id: 1,
    image: project1,
    year: "2024",
    category: "Campaign",
    title: "Northline Brand Campaign",
    description: "A comprehensive brand refresh for a modern lifestyle company.",
  },
  {
    id: 2,
    image: project2,
    year: "2024",
    category: "Editorial",
    title: "Architectural Digest Feature",
    description: "Editorial photography series exploring contemporary spaces.",
  },
  {
    id: 3,
    image: project3,
    year: "2023",
    category: "Product",
    title: "Horizon Product Launch",
    description: "Visual direction for a premium product introduction.",
  },
  {
    id: 4,
    image: project4,
    year: "2023",
    category: "Art Direction",
    title: "Creative Studio Showcase",
    description: "Experimental visual exploration of form and color.",
  },
  {
    id: 5,
    image: project5,
    year: "2023",
    category: "Campaign",
    title: "Essence Lifestyle Series",
    description: "Storytelling through intimate and authentic moments.",
  },
  {
    id: 6,
    image: project6,
    year: "2024",
    category: "Architecture",
    title: "Modern Living Spaces",
    description: "Documenting minimalist design in urban environments.",
  },
];

const SelectedWork = () => {
  return (
    <section id="work" className="section-padding">
      <div className="container-custom">
        <div className="mb-12 md:mb-16">
          <h2 className="text-4xl md:text-5xl font-bold mb-4">Selected Work</h2>
          <p className="text-muted-foreground text-lg">
            A curated collection of recent projects and collaborations.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-12">
          {projects.map((project) => (
            <Card
              key={project.id}
              className="group cursor-pointer bg-card border-0 overflow-hidden transition-transform hover:scale-[1.02]"
            >
              <div className="aspect-[4/3] overflow-hidden bg-muted">
                <img
                  src={project.image}
                  alt={project.title}
                  className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                />
              </div>
              <div className="p-6 space-y-2">
                <p className="text-xs text-label">
                  {project.year} · {project.category}
                </p>
                <h3 className="text-xl font-semibold">{project.title}</h3>
                <p className="text-sm text-muted-foreground">
                  {project.description}
                </p>
              </div>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default SelectedWork;
