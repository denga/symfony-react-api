import { createFileRoute } from '@tanstack/react-router'

export const Route = createFileRoute('/about')({
  component: About,
})

function About() {
  return (
    <main className="page-wrap px-4 py-12">
      <section className="island-shell rounded-2xl p-6 sm:p-8">
        <img
          src="/images/lagoon-about.svg"
          alt=""
          className="mb-6 h-56 w-full rounded-2xl object-cover"
        />
        <p className="island-kicker mb-2">About</p>
        <h1 className="display-title mb-3 text-4xl font-bold text-[var(--sea-ink)] sm:text-5xl">
          Über die Orders App
        </h1>
        <p className="m-0 max-w-3xl text-base leading-8 text-[var(--sea-ink-soft)]">
          Die Orders App ermöglicht die Verwaltung von Bestellungen über eine
          moderne React-Oberfläche. Sie ist an ein Symfony-Backend angebunden und
          nutzt die API-Endpunkte zum Erstellen, Auflisten und Abrufen von
          Orders. Die App verwendet TanStack Router für type-sicheres Routing,
          TanStack Query für effiziente API-Aufrufe, TanStack Table für die
          übersichtliche Darstellung der Order-Liste und TanStack Form für das
          Erstellen neuer Bestellungen mit dynamischen Artikeln.
        </p>
      </section>
    </main>
  )
}
