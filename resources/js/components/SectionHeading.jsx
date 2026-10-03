export function SectionHeading({ eyebrow, title, children, align = 'center' }) {
  const alignCls = align === 'left' ? 'text-left items-start' : 'text-center items-center';
  return (
    <div className={`flex flex-col gap-2 ${alignCls}`}>
      <p className="eyebrow">{eyebrow}</p>
      <h2 className="text-3xl font-bold sm:text-4xl">{title}</h2>
      {children && <p className="max-w-xl whitespace-pre-line text-base text-muted-foreground">{children}</p>}
    </div>
  );
}
