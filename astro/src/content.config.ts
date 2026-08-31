import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const works = defineCollection({
  loader: glob({ pattern: '**/*.json', base: './src/content/works' }),
  schema: z.object({
    title: z.string(),
    method: z.enum(['舗装', 'WJ', 'SB', '床版補修']),
    location: z.string(),
    methodDetail: z.string(),
    scale: z.string(),
    client: z.string().optional(),
    image: z.string(),
    imageAlt: z.string(),
    order: z.number(),
  }),
});

export const collections = { works };
