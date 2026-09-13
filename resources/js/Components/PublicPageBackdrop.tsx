import type { ReactNode } from 'react';

interface PublicPageBackdropProps {
    children: ReactNode;
    image: string;
    imagePosition?: string;
}

export default function PublicPageBackdrop({ children, image, imagePosition = 'center' }: PublicPageBackdropProps) {
    return (
        <div className="relative isolate overflow-hidden bg-slate-50">
            <div
                className="absolute inset-x-0 top-0 -z-20 h-[500px] bg-cover bg-center opacity-[0.42] blur-[2px]"
                style={{ backgroundImage: `url('${image}')`, backgroundPosition: imagePosition }}
                aria-hidden="true"
            />
            <div className="absolute inset-x-0 top-0 -z-10 h-[500px] bg-gradient-to-b from-white/35 via-white/70 to-slate-50" aria-hidden="true" />
            <div className="absolute left-[-8%] top-24 -z-10 h-96 w-96 rounded-full bg-emerald-200/35 blur-3xl" aria-hidden="true" />
            <div className="absolute right-[-6%] top-8 -z-10 h-[28rem] w-[28rem] rounded-full bg-sky-200/35 blur-3xl" aria-hidden="true" />
            {children}
        </div>
    );
}
