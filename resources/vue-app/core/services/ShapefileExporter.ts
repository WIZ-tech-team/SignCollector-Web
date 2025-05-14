import JSZip from 'jszip';
import shpwrite from 'shp-write';
import { DetailedSign } from '@/core/types/data/DetailedSign';
import type { FeatureCollection, Feature, Point } from 'geojson';

export class ShapefileExporter {
    static async exportToShapefile(signs: DetailedSign[], filename: string = 'Signs') {
        try {
            // Validate and prepare data
            const validatedSigns = signs.map(sign => ({
                ...sign,
                longitude: Number(sign.longitude),
                latitude: Number(sign.latitude)
            })).filter(sign => !isNaN(sign.longitude) && !isNaN(sign.latitude));

            if (validatedSigns.length === 0) {
                throw new Error('No valid signs with proper coordinates found');
            }

            const geojson = this.convertToGeoJSON(signs);
            const zipBlob = await this.generateShapefileZip(geojson);
            this.downloadZip(zipBlob, filename);
        } catch (error) {
            console.error('Shapefile export failed:', error);
            throw new Error(`Failed to export shapefile: ${error instanceof Error ? error.message : String(error)}`);
        }
    }

    private static convertToGeoJSON(signs: DetailedSign[]): FeatureCollection {
        return {
            type: 'FeatureCollection',
            features: signs.map((sign): Feature<Point> => ({
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: [Number(sign.longitude), Number(sign.latitude)]
                },
                properties: Object.fromEntries(
                    Object.entries(sign).filter(([key]) => !['longitude', 'latitude'].includes(key))
                )
            }))
        };
    }

    private static async generateShapefileZip(geojson: FeatureCollection): Promise<Blob> {
        return new Promise((resolve, reject) => {
            try {
                // Create a new JSZip instance
                const zip = new JSZip();

                // Generate shapefile components using shpwrite
                shpwrite.download(geojson, {
                    types: { point: 'points' },
                    shp: (shp: ArrayBuffer) => {
                        zip.file('points.shp', shp);
                        return shp;
                    },
                    shx: (shx: ArrayBuffer) => {
                        zip.file('points.shx', shx);
                        return shx;
                    },
                    dbf: (dbf: ArrayBuffer) => {
                        zip.file('points.dbf', dbf);
                        return dbf;
                    },
                    prj: this.getWGS84Projection(),
                    done: () => {
                        // Add PRJ file separately
                        zip.file('points.prj', this.getWGS84Projection());

                        // Generate the zip file
                        zip.generateAsync({ type: 'blob' })
                            .then(resolve)
                            .catch(reject);
                    }
                });
            } catch (error) {
                reject(error);
            }
        });
    }

    private static getWGS84Projection(): string {
        return 'GEOGCS["WGS 84",DATUM["WGS_1984",SPHEROID["WGS 84",6378137,298.257223563]],PRIMEM["Greenwich",0],UNIT["degree",0.0174532925199433]]';
    }

    private static downloadZip(blob: Blob, filename: string) {
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `${filename}_${new Date().toISOString().split('T')[0]}.zip`;
        document.body.appendChild(link);
        link.click();

        // Cleanup
        setTimeout(() => {
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }, 100);
    }
}