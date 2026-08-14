@extends('layouts.app')

@section('title', 'Ce cântăm duminică?')

@section('content')
<section class="home-page">
    <x-home-carousel/>

    

   
</section>

<style>
    .home-page {
    display: grid;
    width: 100%;
    gap: 0;
    padding: 0;
}

    .home-hero {
        display: grid;
        grid-template-columns:
            minmax(0, 1.08fr)
            minmax(330px, 0.92fr);
        align-items: center;
        gap: clamp(35px, 7vw, 90px);
        min-height: 620px;
        padding: clamp(35px, 7vw, 78px);
        overflow: hidden;
        border-radius: 30px;
        background:
            radial-gradient(
                circle at 90% 5%,
                rgba(67, 163, 225, 0.35),
                transparent 31%
            ),
            linear-gradient(
                140deg,
                #05182b,
                #0a335c 67%,
                #155487
            );
        color: #ffffff;
        box-shadow:
            0 28px 75px rgba(4, 28, 51, 0.2);
    }

    .home-eyebrow {
        margin: 0 0 15px;
        color: #8fd5ff;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        text-transform: uppercase;
    }

    .home-hero h1 {
        max-width: 800px;
        margin: 0;
        color: #ffffff;
        font-size: clamp(3rem, 7.5vw, 6.6rem);
        line-height: 0.88;
        letter-spacing: -0.07em;
    }

    .home-hero h1 span {
        display: block;
        color: #92d8ff;
    }

    .home-description {
        max-width: 650px;
        margin: 24px 0 0;
        color: #c6d9e9;
        font-size: 0.96rem;
        line-height: 1.75;
    }

    .home-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 30px;
    }

    .home-primary-button,
    .home-secondary-button {
        display: inline-flex;
        min-height: 50px;
        align-items: center;
        justify-content: center;
        gap: 17px;
        padding: 0 18px;
        border-radius: 12px;
        font-size: 0.78rem;
        font-weight: 900;
        text-decoration: none;
        transition:
            transform 0.2s ease,
            background 0.2s ease;
    }

    .home-primary-button {
        background: #ffffff;
        color: #092847;
    }

    .home-secondary-button {
        border: 1px solid rgba(255, 255, 255, 0.18);
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    .home-primary-button:hover,
    .home-secondary-button:hover {
        transform: translateY(-2px);
    }

    .home-hero-preview {
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.09);
        box-shadow:
            0 28px 65px rgba(0, 0, 0, 0.22);
        backdrop-filter: blur(12px);
        transform: rotate(2deg);
    }

    .home-preview-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    }

    .home-preview-header > div {
        display: flex;
        gap: 6px;
    }

    .home-preview-header > div span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.34);
    }

    .home-preview-header small {
        color: #aac2d5;
        font-size: 0.64rem;
        font-weight: 800;
    }

    .home-preview-body {
        display: grid;
        gap: 9px;
        padding: 22px;
    }

    .home-preview-body > p {
        margin: 0 0 5px;
        color: #8fd5ff;
        font-size: 0.67rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .home-preview-song {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 12px;
        padding: 13px;
        border-radius: 11px;
        background: rgba(3, 21, 39, 0.34);
    }

    .home-preview-song > span {
        display: grid;
        width: 35px;
        height: 35px;
        place-items: center;
        border-radius: 9px;
        background: #dfeefa;
        color: #12679f;
        font-size: 0.72rem;
        font-weight: 900;
    }

    .home-preview-song div {
        display: grid;
        gap: 3px;
    }

    .home-preview-song strong {
        font-size: 0.77rem;
    }

    .home-preview-song small {
        color: #9eb8cc;
        font-size: 0.62rem;
    }

    .home-live-section {
        display: grid;
        gap: 14px;
        padding: 24px;
        border: 1px solid #a9dbbd;
        border-radius: 21px;
        background: #effaf3;
    }

    .home-section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }

    .home-section-heading p,
    .home-section-heading h2 {
        margin: 0;
    }

    .home-section-heading p {
        margin-bottom: 4px;
        color: #197247;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .home-section-heading h2 {
        color: #0b4029;
        font-size: 1.5rem;
    }

    .home-section-heading > span {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 10px;
        background: #d5f3e0;
        color: #176c43;
        font-weight: 900;
    }

    .home-live-list {
        display: grid;
        gap: 9px;
    }

    .home-live-card {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 17px;
        padding: 16px;
        border: 1px solid #c3e7d1;
        border-radius: 14px;
        background: #ffffff;
        color: inherit;
        text-decoration: none;
    }

    .home-live-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 9px;
        border-radius: 999px;
        background: #def6e7;
        color: #147044;
        font-size: 0.62rem;
        font-weight: 900;
        text-transform: uppercase;
    }

    .home-live-status i {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #2bc875;
        box-shadow:
            0 0 0 4px rgba(43, 200, 117, 0.14);
    }

    .home-live-information p,
    .home-live-information h3 {
        margin: 0;
    }

    .home-live-information p {
        margin-bottom: 3px;
        color: #67917b;
        font-size: 0.66rem;
        font-weight: 850;
        text-transform: uppercase;
    }

    .home-live-information h3 {
        color: #0a3724;
        font-size: 1rem;
    }

    .home-live-information > span {
        display: inline-block;
        margin-top: 3px;
        color: #80978b;
        font-size: 0.69rem;
    }

    .home-quick-links {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    .home-quick-links > a {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 14px;
        padding: 20px;
        border: 1px solid #ccd9e4;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.88);
        color: inherit;
        text-decoration: none;
        transition:
            transform 0.2s ease,
            border-color 0.2s ease;
    }

    .home-quick-links > a:hover {
        transform: translateY(-2px);
        border-color: #83b1d1;
    }

    .home-quick-number {
        color: #b2c3d0;
        font-size: 0.67rem;
        font-weight: 900;
    }

    .home-quick-links p,
    .home-quick-links h2 {
        margin: 0;
    }

    .home-quick-links p {
        margin-bottom: 3px;
        color: #1b70a7;
        font-size: 0.62rem;
        font-weight: 900;
        text-transform: uppercase;
    }

    .home-quick-links h2 {
        color: #09223a;
        font-size: 0.94rem;
    }

    .home-quick-links > a > strong {
        color: #1772aa;
    }

    @media (max-width: 850px) {
        .home-hero {
            grid-template-columns: 1fr;
        }

        .home-hero-preview {
            transform: none;
        }

        .home-quick-links {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 550px) {
        .home-hero {
            min-height: 0;
            padding: 35px 24px;
        }

        .home-live-card {
            grid-template-columns: 1fr;
        }

        .home-live-card > strong {
            display: none;
        }
    }

    /*
 * Homepage-ul nu folosește containerul îngust
 * al celorlalte pagini.
 */
.main-content:has(.home-page) {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
}

.home-page {
    width: 100%;
    gap: 0;
    padding: 0;
}

/*
 * Conținutul de sub carusel rămâne încadrat,
 * dar caruselul ocupă tot ecranul.
 */
.home-live-section,
.home-quick-links {
    width: min(1180px, calc(100% - 40px));
    margin-right: auto;
    margin-left: auto;
}

.home-live-section {
    margin-top: 28px;
}

.home-quick-links {
    margin-top: 25px;
    margin-bottom: 50px;
}
</style>
@endsection