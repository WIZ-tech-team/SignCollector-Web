// src/shpwrite.d.ts
declare module 'shp-write' {
  export interface DownloadOptions {
    folder?: string;
    types?: {
      point?: string;
      polygon?: string;
      line?: string;
    };
    compression?: string;
  }

  export function download(
    geojson: GeoJSON.FeatureCollection,
    options?: DownloadOptions
  ): void;
}

// Declare the global shpwrite variable
declare const shpwrite: {
  download: (
    geojson: GeoJSON.FeatureCollection,
    options?: DownloadOptions
  ) => void;
};