import { createFileRoute, Link } from '@tanstack/react-router'

export const Route = createFileRoute('/')({ component: App })

function App() {
  return (
    <main className="page-wrap px-4 pb-8 pt-14">
      <section className="island-shell rise-in relative overflow-hidden rounded-[2rem] px-6 py-10 sm:px-10 sm:py-14">
        <div className="pointer-events-none absolute -left-20 -top-24 h-56 w-56 rounded-full bg-[radial-gradient(circle,rgba(79,184,178,0.32),transparent_66%)]" />
        <div className="pointer-events-none absolute -bottom-20 -right-20 h-56 w-56 rounded-full bg-[radial-gradient(circle,rgba(47,106,74,0.18),transparent_66%)]" />
        <p className="island-kicker mb-3">Orders Management</p>
        <h1 className="display-title mb-5 max-w-3xl text-4xl leading-[1.02] font-bold tracking-tight text-[var(--sea-ink)] sm:text-6xl">
          Bestellungen verwalten – einfach und übersichtlich.
        </h1>
        <p className="mb-8 max-w-2xl text-base text-[var(--sea-ink-soft)] sm:text-lg">
          Verwalten Sie Ihre Orders in einer übersichtlichen Liste. Erstellen Sie
          neue Bestellungen mit dynamischen Artikeln, sehen Sie Details auf einen
          Blick und nutzen Sie die API-Anbindung an das Symfony Backend.
        </p>
        <div className="flex flex-wrap gap-3">
          <Link
            to="/orders"
            className="rounded-full border border-[rgba(50,143,151,0.3)] bg-[rgba(79,184,178,0.14)] px-5 py-2.5 text-sm font-semibold text-[var(--lagoon-deep)] no-underline transition hover:-translate-y-0.5 hover:bg-[rgba(79,184,178,0.24)]"
          >
            Orders anzeigen
          </Link>
          <Link
            to="/orders/create"
            className="rounded-full border border-[rgba(23,58,64,0.2)] bg-white/50 px-5 py-2.5 text-sm font-semibold text-[var(--sea-ink)] no-underline transition hover:-translate-y-0.5 hover:border-[rgba(23,58,64,0.35)]"
          >
            Neue Order
          </Link>
        </div>
      </section>

      <section className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {[
          [
            'Order-Liste',
            'Alle Bestellungen mit Pagination übersichtlich anzeigen.',
          ],
          [
            'Order-Details',
            'Einzelne Orders mit allen Informationen aufrufen.',
          ],
          [
            'Neue Order anlegen',
            'Bestellungen mit dynamischen Artikeln erstellen.',
          ],
          [
            'API-Anbindung',
            'Symfony Backend mit create, list und get Endpoints.',
          ],
        ].map(([title, desc], index) => (
          <article
            key={title}
            className="island-shell feature-card rise-in rounded-2xl p-5"
            style={{ animationDelay: `${index * 90 + 80}ms` }}
          >
            <h2 className="mb-2 text-base font-semibold text-[var(--sea-ink)]">
              {title}
            </h2>
            <p className="m-0 text-sm text-[var(--sea-ink-soft)]">{desc}</p>
          </article>
        ))}
      </section>

      <section className="island-shell mt-8 rounded-2xl p-6">
        <p className="island-kicker mb-2">Schnellzugriff</p>
        <div className="flex flex-wrap gap-3">
          <Link
            to="/orders"
            className="rounded-lg border border-[var(--line)] bg-[var(--surface)] px-4 py-2 text-sm font-medium text-[var(--sea-ink)] no-underline transition hover:bg-[var(--link-bg-hover)]"
          >
            Zur Order-Liste
          </Link>
          <Link
            to="/orders/create"
            className="rounded-lg border border-[var(--line)] bg-[var(--surface)] px-4 py-2 text-sm font-medium text-[var(--sea-ink)] no-underline transition hover:bg-[var(--link-bg-hover)]"
          >
            Neue Order erstellen
          </Link>
          <Link
            to="/about"
            className="rounded-lg border border-[var(--line)] bg-[var(--surface)] px-4 py-2 text-sm font-medium text-[var(--sea-ink)] no-underline transition hover:bg-[var(--link-bg-hover)]"
          >
            Über die App
          </Link>
        </div>
      </section>
    </main>
  )
}
